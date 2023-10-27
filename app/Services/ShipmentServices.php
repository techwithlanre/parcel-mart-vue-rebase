<?php

namespace App\Services;

use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentDestinationRequest;
use App\Http\Requests\CreateShipmentOriginRequest;
use App\Http\Requests\TrackShipmentRequest;
use App\Mail\OrderConfirmation;
use App\Models\AramexShipmentLog;
use App\Models\CourierApiProvider;
use App\Models\DhlRateLog;
use App\Models\DhlShipmentLog;
use App\Models\InsuranceOption;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItem;
use App\Models\ShipmentProvider;
use App\Models\ShippingRateLog;
use App\Models\TrackingLog;
use App\Models\UpsRateLog;
use App\Models\User;
use App\Models\WalletOverdraft;
use App\Models\WalletOverdraftLog;
use App\Models\WalletTransaction;
use App\Traits\TrackingTrait;
use App\Traits\ValidateAramexAddress;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Octw\Aramex\Aramex;

class ShipmentServices
{

    use ValidateAramexAddress;
    use TrackingTrait;
    protected array $errors = [];
    protected bool $aramex_rate_found = false;
    protected bool $dhl_rate_found = false;

    /**
     * @throws ValidationException
     */
    public function calculateShipmentCost($shipment_id): bool|\Illuminate\Http\RedirectResponse
    {
        $shipment_providers = ShipmentProvider::where('shipment_id', $shipment_id)->get();
        foreach ($shipment_providers as $sp) {
            $provider = CourierApiProvider::where('alias', $sp->provider);
            if ($provider->value('status') != 'active') continue;
            $rate_method = $provider->value('rate_method');
            $this->$rate_method($shipment_id, $provider);
        }
        $shipment = Shipment::find($shipment_id);
        if ($shipment->has_rate == 0) throw ValidationException::withMessages(['message' => 'No package found for selected locations']);

        $route = (auth()->user()->is_admin == 1) ? 'admin.shipment.checkout' : 'shipment.checkout';
        return redirect(route($route, $shipment->id));
    }

    private function dhl_calculate_rate($shipment_id, $provider)
    {
        $shipment = Shipment::find($shipment_id);
        $dhl = new DHLServices($shipment);
        try {
            $response = $dhl->calculateRate();
            $data = json_decode($response, true);
            if ($data && array_key_exists('products', $data)) {
                $products = $data['products'];
                foreach ($products as $product) {
                    if ($product['totalPrice'][0]['price'] > 0) {
                        $provider_amount_before_tax = (int) number_format($product['totalPrice'][0]['price'] * 0.925, 2, '.', '');
                        $provider_tax = (int) number_format($product['totalPrice'][0]['price'] * 0.075, 2,  '.', '');
                        $provider_total_amount = $provider_amount_before_tax + $provider_tax;
                        $provider_percentage = CourierApiProvider::where('alias', 'dhl')->value('profit_margin');
                        $charge_before_tax = $provider_amount_before_tax * (1 + ($provider_percentage / 100));
                        $charge_tax = $provider_tax * (1 + ($provider_percentage / 100));
                        $total_charge = $charge_before_tax + $charge_tax;

                        ShippingRateLog::where([
                            'user_id' => $shipment->user_id,
                            'shipment_id' => $shipment->id,
                            'courier_api_provider_id' => $provider->value('id'),
                        ])->delete();

                        $rate_log = ShippingRateLog::create([
                            'user_id' => $shipment->user_id,
                            'shipment_id' => $shipment->id,
                            'courier_api_provider_id' => $provider->value('id'),
                            'product_name' => 'DHL - ' . $product['productName'],
                            'product_code' => $product['productCode'],
                            'local_product_code' => $product['localProductCode'],
                            'network_type_code' => $product['networkTypeCode'],
                            'provider_code' => 'dhl',
                            'currency' => 'NGN',
                            'provider_amount_before_tax' => $provider_amount_before_tax,
                            'provider_tax' => $provider_tax,
                            'provider_total_amount' => $provider_total_amount,
                            'charge_before_tax' => $charge_before_tax,
                            'charge_tax' => $charge_tax,
                            'total_charge' => $total_charge,
                            'created_at' => now()
                        ]);

                        DhlRateLog::where([
                            'shipment_id' => $shipment->id,
                            'product_name' => $product['productName'],
                            'product_code' => $product['productCode'],
                            'local_product_code' => $product['localProductCode'],
                            'network_type_code' => $product['networkTypeCode'],
                        ])->delete();

                        DhlRateLog::create([
                            'shipment_id' => $shipment->id,
                            'shipping_rate_log_id' => $rate_log->id,
                            'product_name' => $product['productName'],
                            'product_code' => $product['productCode'],
                            'amount' => number_format($product['totalPrice'][0]['price'] * 0.925, 2),
                            'tax' => number_format($product['totalPrice'][0]['price'] * 0.075, 2),
                            'total_amount' => number_format(($product['totalPrice'][0]['price'] * 0.925) + ($product['totalPrice'][0]['price'] * 0.075), 2),
                            'local_product_code' => $product['localProductCode'],
                            'network_type_code' => $product['networkTypeCode'],
                            'weight' => json_encode($product['weight']),
                            'total_price_breakdown' => isset($product['totalPriceBreakdown']) ? json_encode($product['totalPriceBreakdown']) : '',
                            'total_price' => json_encode($product['totalPrice']),
                            'detailed_price_breakdown' => json_encode($product['detailedPriceBreakdown']),
                            'pickup_capabilities' => json_encode($product['pickupCapabilities']),
                            'delivery_capabilities' => json_encode($product['deliveryCapabilities']),
                            'pricing_date' => $product['pricingDate'],
                        ]);
                    }

                    $shipment->has_rate = 1;
                    $shipment->save();
                    $this->dhl_rate_found = true;
                }
            } else {
                activity()
                    ->performedOn(new Shipment())
                    ->causedBy(\request()->user())
                    ->withProperties([
                        'method' => __FUNCTION__,
                        'action' => 'DHL Shipment Cost'
                    ])
                    ->log($response);
            }
        } catch (\Throwable $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'DHL Shipment Cost'
                ])
                ->log($e->getMessage());
            $this->errors[] = 'DHL shipment is not available at the moment.';
        }
    }

    private function aramex_calculate_rate($shipment_id, $provider)
    {
        $shipment = Shipment::find($shipment_id);
        $aramex = new AramexServices($shipment);
        try {
            $response = $aramex->calculateShippingRate();
            if ($response) {
                $provider_amount_before_tax = $response->RateDetails->TotalAmountBeforeTax;
                $provider_tax = $response->RateDetails->TaxAmount;
                $provider_total_amount = $provider_amount_before_tax + $provider_tax;
                $provider_percentage = $provider->value('profit_margin');
                $charge_before_tax = $provider_amount_before_tax * (1 + ($provider_percentage / 100));
                $charge_tax = $provider_tax * (1 + ($provider_percentage / 100));
                $total_charge = $charge_before_tax + $charge_tax;

                ShippingRateLog::where([
                    'user_id' => $shipment->user_id,
                    'shipment_id'=>$shipment_id,
                    'courier_api_provider_id' => $provider->value('id'),
                    'provider_code' => 'aramex',
                ])->delete();

                $rate = ShippingRateLog::create([
                    'user_id' => $shipment->user_id,
                    'shipment_id' => $shipment_id,
                    'product_name' => 'Aramex Shipping',
                    'courier_api_provider_id' => $provider->value('id'),
                    'currency' => $response->TotalAmount->CurrencyCode,
                    'provider_amount_before_tax' => $provider_amount_before_tax,
                    'provider_tax' => $provider_tax,
                    'provider_total_amount' => $provider_total_amount,
                    'charge_before_tax' => $charge_before_tax,
                    'charge_tax' => $charge_tax,
                    'total_charge' => $total_charge,
                    'provider_code' => 'aramex',
                    'created_at' => now()
                ]);

                $shipment->has_rate = 1;
                $shipment->save();
                $this->aramex_rate_found = true;
            } else {
                activity()
                    ->performedOn(new Shipment())
                    ->causedBy(\request()->user())
                    ->withProperties([
                        'method' => __FUNCTION__,
                        'action' => 'Aramex Shipment Cost'
                    ])
                    ->log($response);
                ShippingRateLog::where([
                    'user_id' => $shipment->user_id,
                    'shipment_id'=>$shipment_id,
                    'courier_api_provider_id' => $provider->value('id')
                ])->delete();
            }
        } catch (\Throwable $e) {
            ShippingRateLog::where([
                'user_id' => $shipment->user_id,
                'shipment_id'=>$shipment_id,
                'courier_api_provider_id' => $provider->value('id')
            ])->delete();
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Aramex Recalculate Rate'
                ])
                ->log($e->getMessage());
            $this->errors[] = 'Aramex shipment is not available at the moment.';
        }
    }

    private function ups_calculate_rate($shipment_id, $provider)
    {
        $shipment = Shipment::find($shipment_id);
        $ups = new UpsServices($shipment);
        try {
            $response = $ups->calculateRate();
            $data = json_decode($response, true);
            if (array_key_exists('ResponseStatus', $data['RateResponse']['Response'])) {
                if ($data['RateResponse']['Response']['ResponseStatus']['Code'] == 1) {
                    $ratedShipment = $data['RateResponse']['RatedShipment'];
                    if ($ratedShipment['TotalCharges']['MonetaryValue'] > 0) {
                        $provider_total_amount = $ratedShipment['TotalCharges']['MonetaryValue'];
                        $provider_tax = $ratedShipment['TotalCharges']['MonetaryValue'] * 0.075;
                        $provider_amount_before_tax = $provider_total_amount - $provider_tax;
                        $multiplier = ($provider->value('profit_margin') / 100) + 1;
                        $total_charge = $ratedShipment['TotalCharges']['MonetaryValue'] * $multiplier;
                        $charge_tax = $total_charge * 0.075;
                        $charge_before_tax = $total_charge - $charge_tax;

                        ShippingRateLog::where([
                            'user_id' => $shipment->user_id,
                            'shipment_id'=>$shipment_id,
                            'courier_api_provider_id' => $provider->value('id'),
                            'provider_code' => 'ups',
                        ])->delete();

                        $rate_log = ShippingRateLog::create([
                            'user_id' => $shipment->user_id,
                            'shipment_id' => $shipment->id,
                            'courier_api_provider_id' => $provider->value('id'),
                            'product_name' => 'UPS Shipment',
                            'product_code' => '',
                            'provider_code' => 'ups',
                            'currency' => 'NGN',
                            'provider_amount_before_tax' => $provider_amount_before_tax,
                            'provider_tax' => $provider_tax,
                            'provider_total_amount' => $provider_total_amount,
                            'charge_before_tax' => $charge_before_tax,
                            'charge_tax' => $charge_tax,
                            'total_charge' => $total_charge,
                            'created_at' => now(),
                        ]);

                        UpsRateLog::create([
                            'shipment_id' => $shipment->id,
                            'shipping_rate_log_id' => $rate_log->id,
                            'service_code' => $ratedShipment['Service']['Code'],
                            'packaging_type_code' => '02',
                            'billing_weight' => json_encode($ratedShipment['BillingWeight']),
                            'total_price' => json_encode($ratedShipment['TotalCharges']),
                            'total_price_breakdown' => json_encode($ratedShipment),
                            'pricing_date' => now(),
                        ]);

                        $shipment->has_rate = 1;
                        $shipment->save();
                        $ups_rate_found = true;
                    } else {
                        activity()
                            ->performedOn(new Shipment())
                            ->causedBy(\request()->user())
                            ->withProperties([
                                'method' => __FUNCTION__,
                                'action' => 'UPS Shipment Cost'
                            ])
                            ->log($response);
                    }

                    if (!$ups_rate_found) {
                        ShippingRateLog::where([
                            'user_id' => $shipment->user_id,
                            'shipment_id'=>$shipment_id,
                            'courier_api_provider_id' => $provider->value('id'),
                            'provider_code' => 'ups',
                        ])->delete();
                    }
                }
            }
        } catch (\Throwable $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'UPS Shipment Cost'
                ])
                ->log($e->getMessage());
            $this->errors[] = 'UPS shipment is not available at the moment.';
        }
    }

    /**
     * @throws ValidationException
     */
    public function bookShipment(BookShipmentRequest $request): \Illuminate\Foundation\Application|false|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $shipment = Shipment::whereId($request->shipment_id)->first();
        $user = User::find($shipment->user_id);
        $shipment_item = ShipmentItem::where('shipment_id', $shipment->id)->first();
        $insurance = InsuranceOption::whereId($request->insurance)->first();
        $insurance_amount = $insurance->amount;
        $rate = ShippingRateLog::whereId($request->option_id)->first();
        $total_amount = $rate->total_charge + $insurance_amount;

        if ($user->user_type === 'individual' && $user->balance < $total_amount) {
            $message = ($user->is_admin == 0) ? 'Insufficient balance. Notify user to fund their wallet' : 'Insufficient balance. Fund your wallet to continue';
            throw ValidationException::withMessages(['message' => $message]);
        }

        $current_balance = $user->balance;
        if ($user->user_type === 'business') {
            $current_overdraft_amount = $total_amount - $current_balance; // loan amount at the moment
            $overdraft_wallet = WalletOverdraft::where('user_id', $user->id)->first();
            if (!$overdraft_wallet) {
                $overdraft_wallet = WalletOverdraft::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    ])->first();
            }
            $previous_overdraft = $overdraft_wallet->balance;
            $total_overdraft = $current_overdraft_amount + $previous_overdraft;
            if ($total_overdraft > $user->credit_limit) {
                throw ValidationException::withMessages(['message' => 'Insufficient balance: Order amount is above your credit limit']);
            }
        }

        $provider = $rate->provider_code;
        $book_aramex = $book_dhl = $book_ups =  false;
        if ($provider == 'aramex') {
            $aramex = new AramexServices($shipment);
            $pickup = $aramex->createPickup($request, $shipment_item);
            if (!$pickup) {
                activity()
                    ->performedOn(new Shipment())
                    ->causedBy(\request()->user())
                    ->withProperties([
                        'method' => __FUNCTION__,
                        'action' => 'Aramex create pickup'
                    ])
                    ->log($pickup);
                throw ValidationException::withMessages(['message' => 'We were not able to process pickup. Please try again later']);
            }

            if ($pickup->error == 0) {
                $rate->pickup_number = $pickup->pickupID;
                $rate->save();
                $book_aramex = $aramex->bookShipment($request, $shipment_item, $insurance, $rate, $pickup->pickupGUID);
            }
        }

        if ($provider == 'dhl') {
            $dhl = new DHLServices($shipment);
            $book_dhl = $dhl->bookShipment($shipment_item, $insurance, $rate, $request);
        }

        if ($provider == 'ups') {
            $ups = new UpsServices($shipment);
            $book_ups = $ups->bookShipment($shipment_item, $request, $rate);
        }

        if ($book_aramex || $book_dhl || $book_ups) {
            if ($user->user_type === 'business' && $user->balance < $total_amount) {
                $current_balance = $user->balance;
                $overdraft_amount = $total_amount - $current_balance;
                $overdraft_wallet = WalletOverdraft::where('user_id', $user->id)->first();
                $overdraft_wallet->balance += $overdraft_amount;
                $overdraft_wallet->save();

                WalletOverdraftLog::create([
                    'user_id' => $user->id,
                    'shipment_id' => $shipment->id,
                    'amount' => $overdraft_amount
                ]);
                $user->withdraw($current_balance);
            }

            $transaction = '';
            if ($user->user_type === 'business' && $user->balance >= $total_amount)  {
                $transaction = $user->withdraw($total_amount);
            }
            if ($user->user_type === 'individual') {
                $transaction = $user->withdraw($total_amount);
            }

            $rate->insurance_option_id = $insurance->id;
            $rate->insurance_amount = $insurance_amount;
            $rate->save();

            $shipment->provider_id = $rate->courier_api_provider_id;
            $shipment->shipping_rate_log_id  = $rate->id;
            $shipment->provider = $provider;
            $shipment->is_paid = 1;
            $shipment->status = 'processing';
            $shipment->save();

            //log transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'reference' => $transaction->uuid,
                'status' => 'success',
                'amount' => $total_amount,
                'before' => $current_balance,
                'after' => $user->balance,
                'comment' => 'shipment',
                'description' => 'payment',
                'currency' => 'NGN',
                'time_initiated' => now(),
                'channel' => 'wallet'
            ]);

            //fire email
            Mail::to($user->email)->send(new OrderConfirmation([
                'shipment' => $shipment,
                'shipment_item' => $shipment_item,
                'address' => ShipmentAddress::where('shipment_id', $shipment->id)->get()
            ]));
        }

        if ($provider == 'dhl' && !$book_dhl) {
            $route = (auth()->user()->is_admin == 1) ? 'admin.shipment.checkout' : 'shipment.checkout';
            return redirect(route($route, $request['shipment_id']))->with('error', 'DHL shipment is not available for selected locations at the moment. Please try again later');
        }

        if ($provider == 'aramex'  && !$book_aramex) {
            $route = (auth()->user()->is_admin == 1) ? 'admin.shipment.checkout' : 'shipment.checkout';
            return redirect(route($route, $request['shipment_id']))->with('error', 'Aramex shipment is not available for selected locations at the moment. Please try again later');
        }

        $route = (auth()->user()->is_admin == 1) ? 'admin.shipment.details' : 'shipment.details';
        return \redirect(route($route, $shipment->id));
    }

    private function bookWithAramex($bookShipmentRequest, $shipment, $shipment_item)
    {

    }

    /**
     * @throws ValidationException
     */
    public function trackShipment(TrackShipmentRequest $request)
    {
        $shipment_number = trim($request->number);
        $shipment = Shipment::where([
            'number' => $shipment_number,
            'user_id' => auth()->user()->id,
        ])->first();

        if (!$shipment) throw ValidationException::withMessages(['number' => 'Tracking number not found']);
        if ($shipment->provider == 'aramex') return $this->trackAramex($shipment);
        if ($shipment->provider == 'dhl') return $this->trackDhl($request, $shipment);
        return redirect()->back()->with('error', 'An error occurred. Please try again later');
    }

    public function homeTrackShipment(TrackShipmentRequest $request)
    {
        $shipment_number = trim($request->number);
        $shipment = Shipment::where([
            'number' => $shipment_number,
        ])->first();

        if (!$shipment) throw ValidationException::withMessages(['number' => 'Tracking number not found']);
        if ($shipment->provider == 'aramex') return $this->homeTrackAramex($shipment);
        if ($shipment->provider == 'dhl') return $this->homeTrackDhl($request, $shipment);
        return redirect()->back()->with('error', 'An error occurred. Please try again later');
    }

    private function trackAramex(Shipment $shipment): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $aramex_shipment = AramexShipmentLog::where('shipment_id', $shipment->id)->first();
        try {
            $response = Aramex::trackShipments([$aramex_shipment->aramex_id]);
            if (!$response->HasErrors) {
                $data = $response->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY->Value->TrackingResult;
                $check = TrackingLog::where([
                    'shipment_id' => $shipment->id,
                    'update_code' => $data->UpdateCode,
                    'provider' => 'aramex',
                ])->first();

                if (!$check) {
                    TrackingLog::create([
                        'shipment_id' => $shipment->id,
                        'update_code' => $data->UpdateCode,
                        'waybill_number' => $data->WaybillNumber,
                        'update_description' => $data->UpdateDescription,
                        'update_datetime' => $data->UpdateDateTime,
                        'update_location' => $data->UpdateLocation,
                        'comment' => $data->Comments,
                        'problem_code' => $data->ProblemCode,
                        'gross_weight' => $data->GrossWeight,
                        'chargeable_weight' => $data->ChargeableWeight,
                        'weight_unit' => $data->WeightUnit,
                        'provider' => 'aramex',
                    ]);
                    return \redirect(route('shipment.track.details', $shipment->id));
                }

                return \redirect(route('shipment.track.details', $shipment->id));
            }

            //return redirect()->back()->with('error', 'We are working on tracking info. Please check back later');
            throw ValidationException::withMessages(['number' => 'We are working on tracking info. Please check back later']);
        } catch (\Throwable  $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Aramex Track Shipment'
                ])
                ->log($e->getMessage());
            throw ValidationException::withMessages(['number' => 'We are working on tracking info. Please check back later xxxxxxx']);
        }

    }

    private function trackDhl(TrackShipmentRequest $request, Shipment $shipment)
    {
        $dhl_shipment = DhlShipmentLog::where('shipment_id', $shipment->id)->first();
        if (!$dhl_shipment) return redirect()->back()->with('error', 'An error occurred. Please try again later');
        try {
            $dhl = new DHLServices($shipment);
            $response = $dhl->trackShipment($dhl_shipment);
            $tracking_data = json_decode($response, true);
            $tracking_log = $this->saveDhlTracking($tracking_data['shipments'], $shipment->id);
            if ($tracking_log) return \redirect(route('shipment.track.details', $shipment->id));
            throw ValidationException::withMessages(['number' => 'We are working on tracking info. Please check back later']);
        } catch (\Throwable $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'DHL Track Shipment'
                ])
                ->log($e->getMessage());
            throw ValidationException::withMessages(['number' => 'We are working on tracking info. Please check back later']);
        }
    }

    private function homeTrackAramex(Shipment $shipment): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $aramex_shipment = AramexShipmentLog::where('shipment_id', $shipment->id)->first();
        try {
            $response = Aramex::trackShipments([$aramex_shipment->aramex_id]);
            if (!$response->HasErrors) {
                $data = $response->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY->Value->TrackingResult;
                $check = TrackingLog::where([
                    'shipment_id' => $shipment->id,
                    'update_code' => $data->UpdateCode,
                    'provider' => 'aramex',
                ])->first();

                if (!$check) {
                    TrackingLog::create([
                        'shipment_id' => $shipment->id,
                        'update_code' => $data->UpdateCode,
                        'waybill_number' => $data->WaybillNumber,
                        'update_description' => $data->UpdateDescription,
                        'update_datetime' => $data->UpdateDateTime,
                        'update_location' => $data->UpdateLocation,
                        'comment' => $data->Comments,
                        'problem_code' => $data->ProblemCode,
                        'gross_weight' => $data->GrossWeight,
                        'chargeable_weight' => $data->ChargeableWeight,
                        'weight_unit' => $data->WeightUnit,
                        'provider' => 'aramex',
                    ]);
                    return \redirect(route('home.shipment.track.details', $shipment->id));
                }

                return \redirect(route('home.shipment.track.details', $shipment->id));
            }

            //return redirect()->back()->with('error', 'We are working on tracking info. Please check back later');
            throw ValidationException::withMessages(['number' => 'We are working on tracking info. Please check back later']);
        } catch (\Throwable  $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Aramex Track Shipment'
                ])
                ->log($e->getMessage());
            throw ValidationException::withMessages(['number' => 'We are working on tracking info. Please check back later xxxxxxx']);
        }

    }

    private function homeTrackDhl(TrackShipmentRequest $request, Shipment $shipment)
    {
        $dhl_shipment = DhlShipmentLog::where('shipment_id', $shipment->id)->first();
        if (!$dhl_shipment) return redirect()->back()->with('error', 'An error occurred. Please try again later');
        try {
            $dhl = new DHLServices($shipment);
            $response = $dhl->trackShipment($dhl_shipment);
            $tracking_data = json_decode($response, true);
            $tracking_log = $this->saveDhlTracking($tracking_data['shipments'], $shipment->id);
            if ($tracking_log) return \redirect(route('home.shipment.track.details', $shipment->id));
            throw ValidationException::withMessages(['number' => 'We are working on tracking info. Please check back later']);
        } catch (\Throwable $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'DHL Track Shipment'
                ])
                ->log($e->getMessage());
            throw ValidationException::withMessages(['number' => 'We are working on tracking info. Please check back later']);
        }
    }

    public function calculatePickup(BookShipmentRequest $request, ShippingRateLog $rate_log): void
    {
        $shipment = Shipment::find($rate_log->shipment_id);
        $shipment_items = ShipmentItem::where('shipment_id', $rate_log->shipment_id)->first();
        if ($rate_log->shipment_number == NULL && $rate_log->provider_code == 'dhl') {
            $dhl = new DHLServices($request);
            $pickup = $dhl->calculatePickup($shipment, $shipment_items, $request);
            $rate_log->pickup_number = $pickup['dispatchConfirmationNumbers'][0];
            $rate_log->save();
            return;
        }

        if ($rate_log->shipment_number == NULL && $rate_log->provider_code == 'aramex') {
            $dhl = new AramexServices($request);
            $pickup = $dhl->createPickup($shipment, $shipment_items, $request);
            $rate_log->pickup_number = $pickup['dispatchConfirmationNumbers'][0];
            $rate_log->save();
            return;
        }
    }

    /**
     * @throws GuzzleException
     */
    public function validateAddress(CreateShipmentOriginRequest | CreateShipmentDestinationRequest $request, $shipment_id, $type = 'pickup'): bool
    {
        $check_aramex = CourierApiProvider::where('alias', 'aramex');
        if ($check_aramex->value('status') == 'active') {
            try {
                $response = Aramex::validateAddress([
                    'line1' => $request->address_1,
                    'line2' => $request->landmark,
                    'line3' => $request->address_2,
                    'country_code' => getCountry('id', $request->country_id)->iso2,
                    'postal_code' => $request->postcode,
                    'city' => getCity('id', $request->city_id)->name,
                ]);

                //ups does not allow for address validation, so we send request to them by default for rate calculation
                ShipmentProvider::updateOrCreate([
                    'shipment_id' => $shipment_id,
                    'provider' => 'ups'
                ]);

                if (!isset($response->error)) {
                    ShipmentProvider::updateOrCreate([
                        'shipment_id' => $shipment_id,
                        'provider' => 'aramex'
                    ]);
                } else {
                    ShipmentProvider::where([
                        'shipment_id' => $shipment_id,
                        'provider' => 'aramex'
                    ])->delete();
                }
            } catch (\Throwable $e) {
                activity()
                    ->causedBy(\request()->user())
                    ->withProperties([
                        'class' => __CLASS__,
                        'method' => __FUNCTION__,
                        'action' => 'DHL Validate Address rate',
                        'line' => $e->getLine()
                    ])
                    ->log($e->getMessage());
            }
        }

        $check_dhl = CourierApiProvider::where('alias', 'dhl');
        if ($check_dhl->value('status') == 'active') {
            $dhl_env = config('dhl.ENV');
            $dhl_base_url = config('dhl.'.$dhl_env.'.baseUrl');
            $dhl_username = config('dhl.'.$dhl_env.'.username');
            $dhl_password = config('dhl.'.$dhl_env.'.password');
            try {
                $response = Http::withBasicAuth($dhl_username, $dhl_password)
                    ->get("$dhl_base_url/address-validate", [
                    'type' => $type,
                    'countryCode' => getCountry('id', $request->country_id)->iso2,
                    'postalCode' => $request->postcode,
                    'cityName' => getCity('id', $request->city_id)->name,
                    'strictValidation' => "true"
                ]);

                if ($response->status() == 200) {
                    ShipmentProvider::updateOrCreate([
                        'shipment_id' => $shipment_id,
                        'provider' => 'dhl'
                    ]);
                } else {
                    ShipmentProvider::where([
                        'shipment_id' => $shipment_id,
                        'provider' => 'dhl'
                    ])->delete();
                }
            } catch(\Throwable $e) {
                activity()
                    ->causedBy(\request()->user())
                    ->withProperties([
                        'class' => __CLASS__,
                        'method' => __FUNCTION__,
                        'action' => 'DHL Validate Address rate',
                        'line' => $e->getLine()
                    ])
                    ->log($e->getMessage());
            }
        }

        ShipmentProvider::updateOrCreate([
            'shipment_id' => $shipment_id,
            'provider' => 'ups'
        ]);
        return true;
    }
}
