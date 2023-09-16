<?php

namespace App\Services;


use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentDestinationRequest;
use App\Http\Requests\CreateShipmentOriginRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Requests\TrackShipmentRequest;
use App\Mail\OrderConfirmation;
use App\Models\AramexShipmentLog;
use App\Models\CourierApiProvider;
use App\Models\DhlRateLog;
use App\Models\DhlShipmentLog;
use App\Models\InsuranceOption;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentProvider;
use App\Models\ShippingRateLog;
use App\Models\TrackingLog;
use App\Models\UpsRateLog;
use App\Models\WalletOverdraft;
use App\Models\WalletOverdraftLog;
use App\Traits\ValidateAramexAddress;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Octw\Aramex\Aramex;
use GuzzleHttp\Client;

class OldShipmentServices
{

    use ValidateAramexAddress;

    protected array $errors = [];
    protected bool $aramex_rate_found = false;
    protected bool $dhl_rate_found = false;

    public function logRecalculateShipment(CreateShipmentRequest $request)
    {
        $shipment = Shipment::find(request()->id);
        $shipment->origin_address = json_encode($request->origin);
        $shipment->destination_address = json_encode($request->destination);
        $shipment->save();

        $shipment_item = ShipmentItem::where('shipment_id', $shipment->id)->first();
        $shipment_item->item_category_id = $request->shipment['category'];
        $shipment_item->description = $request->shipment['description'];
        $shipment_item->quantity = $request->shipment['quantity'];
        $shipment_item->weight = $request->shipment['weight'];
        $shipment_item->height = $request->shipment['height'];
        $shipment_item->width = $request->shipment['width'];
        $shipment_item->value = $request->shipment['value'];
        $shipment_item->save();

        return $shipment;
    }

    public function recalculateShipmentCost(CreateShipmentRequest $request): bool|\Illuminate\Http\RedirectResponse
    {
        $shipment_id = request()->id;
        $shipment = Shipment::find($shipment_id);

        $check_aramex = CourierApiProvider::where('alias', 'aramex');
        $aramex_rate_found = $dhl_rate_found = $ups_rate_found = false;
        if ($check_aramex->value('status') == 'active') {
            $aramex = new AramexServices($request);
            try {
                $response = $aramex->calculateShippingRate();
                if ($response) {
                    ShippingRateLog::where([
                        'user_id' => auth()->user()->id,
                        'shipment_id'=>$shipment_id,
                        'courier_api_provider_id' => $check_aramex->value('id'),
                        'provider_code' => 'aramex',
                    ])->delete();

                    ShippingRateLog::create([
                        'user_id' => auth()->user()->id,
                        'shipment_id'=>$shipment_id,
                        'courier_api_provider_id' => $check_aramex->value('id'),
                        'provider_code' => 'aramex',
                        'product_name' => 'Aramex Shipping',
                        'currency' => $response->TotalAmount->CurrencyCode,
                        'total_amount' => $response->TotalAmount->Value,
                        'amount_before_tax' => $response->RateDetails->TotalAmountBeforeTax,
                        'tax' => $response->RateDetails->TaxAmount,
                        'created_at' => now()
                    ]);

                    $aramex_rate_found = true;
                }
                if (!$aramex_rate_found) {
                    ShippingRateLog::where([
                        'user_id' => auth()->user()->id,
                        'shipment_id'=>$shipment_id,
                        'courier_api_provider_id' => $check_aramex->value('id'),
                        'provider_code' => 'aramex',
                    ])->delete();
                }
            } catch (\Throwable $e) {
                activity()
                    ->performedOn(new Shipment())
                    ->causedBy(\request()->user())
                    ->withProperties([
                        'method' => __FUNCTION__,
                        'action' => 'Aramex Recalculate Rate'
                    ])
                    ->log($e->getMessage());
                $this->errors[] = 'Aramex shipment is not available at the moment. ';
            }

        }

        $check_dhl = CourierApiProvider::where('alias', 'dhl');
        if ($check_dhl->value('status') == 'active') {
            $dhl = new DHLServices($request);
            try {
                $response = $dhl->calculateRate();
                $data = json_decode($response, true);
                if ($data && array_key_exists('products', $data)) {
                    $products = $data['products'];
                    foreach ($products as $product) {
                        if ($product['totalPrice'][0]['price'] > 0) {
                            ShippingRateLog::where([
                                'user_id' => auth()->user()->id,
                                'shipment_id'=>$shipment_id,
                                'courier_api_provider_id' => $check_dhl->value('id'),
                                'provider_code' => 'dhl',
                            ])->delete();

                            ShippingRateLog::create([
                                'user_id' => auth()->user()->id,
                                'shipment_id' => $shipment->id,
                                'courier_api_provider_id' => $check_dhl->value('id'),
                                'product_name' => 'DHL - ' . $product['productName'],
                                'product_code' => $product['productCode'],
                                'local_product_code' => $product['localProductCode'],
                                'network_type_code' => $product['networkTypeCode'],
                                'provider_code' => 'dhl',
                                'currency' => 'NGN',
                                'total_amount' => number_format(($product['totalPrice'][0]['price'] * 0.925) + ($product['totalPrice'][0]['price'] * 0.075), 2),
                                'amount_before_tax' => number_format($product['totalPrice'][0]['price'] * 0.925, 2),
                                'tax' => number_format($product['totalPrice'][0]['price'] * 0.075, 2),
                                'created_at' => now()
                            ]);

                            DhlRateLog::where(['shipment_id' => $shipment->id,'product_code' => $product['productCode']])->update([
                                'product_name' => $product['productName'],
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

                        $dhl_rate_found = true;
                    }
                }
            } catch (\Throwable $e) {
                activity()
                    ->performedOn(new Shipment())
                    ->causedBy(\request()->user())
                    ->withProperties([
                        'method' => __FUNCTION__,
                        'action' => 'DHL Recalculate Rate'
                    ])
                    ->log($e->getMessage());
                $this->errors[] = 'DHL shipment is not available at the moment.';
            }
        }

        $check_ups = CourierApiProvider::where('alias', 'ups');
        if ($check_ups->value('status') == 'active') {
            $ups = new UpsServices($request);
            try {
                $response = $ups->calculateRate();
                $data = json_decode($response, true);
                if (array_key_exists('ResponseStatus', $data['RateResponse']['Response'])) {
                    if ($data['RateResponse']['Response']['ResponseStatus']['Code'] == 1) {
                        $ratedShipment = $data['RateResponse']['RatedShipment'];
                        if ($ratedShipment['TotalCharges']['MonetaryValue'] > 0) {
                            $multiplier = ($check_ups->value('profit_margin') / 100) + 1;
                            $shipmentAmount = $ratedShipment['TotalCharges']['MonetaryValue'] * $multiplier;
                            $tax = $shipmentAmount * 0.075;
                            ShippingRateLog::where([
                                'user_id' => auth()->user()->id,
                                'shipment_id'=>$shipment_id,
                                'courier_api_provider_id' => $check_ups->value('id'),
                                'provider_code' => 'ups',
                            ])->delete();

                            $rate_log = ShippingRateLog::create([
                                'user_id' => auth()->user()->id,
                                'shipment_id' => $shipment->id,
                                'courier_api_provider_id' => $check_ups->value('id'),
                                'product_name' => 'UPS Shipment',
                                'product_code' => '',
                                'provider_code' => 'ups',
                                'currency' => 'NGN',
                                'total_amount' => $shipmentAmount,
                                'amount_before_tax' => number_format($shipmentAmount - $tax),
                                'tax' => number_format($tax),
                                'created_at' => now()
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
                                'user_id' => auth()->user()->id,
                                'shipment_id'=>$shipment_id,
                                'courier_api_provider_id' => $check_ups->value('id'),
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

        if (count($this->errors) > 0) {
            return Redirect::back()->with('error', implode("|", $this->errors));
        }

        if (!$aramex_rate_found && !$dhl_rate_found && !$ups_rate_found) {
            return Redirect::back()->with('error', 'No shipment rate found for your package');
        }

        if ($aramex_rate_found || $dhl_rate_found || $ups_rate_found) {
            $this->logRecalculateShipment($request);
            return Redirect::route('shipment.checkout', $shipment->id);
        }

        return Redirect::back()->with('error', 'No shipment rate found for your package');
    }

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

        return redirect(route('shipment.checkout', $shipment->id));

        /*$check_dhl = CourierApiProvider::where('alias', 'dhl');
        $check_ups = CourierApiProvider::where('alias', 'ups');
        if ($check_ups->value('status') == 'active') {
            $ups = new UpsServices($request);
            try {
                $response = $ups->calculateRate();
                $data = json_decode($response, true);
                if (array_key_exists('ResponseStatus', $data['RateResponse']['Response'])) {
                    if ($data['RateResponse']['Response']['ResponseStatus']['Code'] == 1) {
                        $ratedShipment = $data['RateResponse']['RatedShipment'];
                        if ($ratedShipment['TotalCharges']['MonetaryValue'] > 0) {
                            $multiplier = ($check_ups->value('profit_margin') / 100) + 1;
                            $shipmentAmount = $ratedShipment['TotalCharges']['MonetaryValue'] * $multiplier;
                            $tax = $shipmentAmount * 0.075;
                            $rate_log = ShippingRateLog::create([
                                'user_id' => auth()->user()->id,
                                'shipment_id' => $shipment->id,
                                'courier_api_provider_id' => $check_ups->value('id'),
                                'product_name' => 'UPS Shipment',
                                'product_code' => '08',
                                'provider_code' => 'ups',
                                'currency' => 'NGN',
                                'total_amount' => $shipmentAmount,
                                'amount_before_tax' => number_format($shipmentAmount - $tax),
                                'tax' => number_format($tax),
                                'created_at' => now()
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
                            $rate_found = true;
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

        if (!$rate_found) {
            $shipment->status = 'failed';
            $shipment->save();
            throw ValidationException::withMessages(['message' => 'No package found for locations selected']);
        }

        return Redirect::route('shipment.checkout', $shipment->id);*/
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
                            'user_id' => auth()->user()->id,
                            'shipment_id' => $shipment->id,
                            'courier_api_provider_id' => $provider->value('id'),
                            'product_name' => 'DHL - ' . $product['productName'],
                            'product_code' => $product['productCode'],
                            'local_product_code' => $product['localProductCode'],
                            'network_type_code' => $product['networkTypeCode']
                        ])->delete();

                        $rate_log = ShippingRateLog::create([
                            'user_id' => auth()->user()->id,
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

                        $dhl_rate_log = DhlRateLog::create([
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
                    'user_id' => auth()->user()->id,
                    'shipment_id'=>$shipment_id,
                    'courier_api_provider_id' => $provider->value('id'),
                    'provider_code' => 'aramex',
                ])->delete();

                $rate = ShippingRateLog::create([
                    'user_id' => auth()->user()->id,
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
            }
        } catch (\Throwable $e) {
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

    /**
     * @throws ValidationException
     */
    public function bookShipment(BookShipmentRequest $request): \Illuminate\Foundation\Application|false|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $shipment = Shipment::whereId($request->shipment_id)->first();
        $shipment_item = ShipmentItem::where('shipment_id', $shipment->id)->first();
        $insurance = InsuranceOption::whereId($request->insurance)->first();
        $insurance_amount = $insurance->amount;
        $rate = ShippingRateLog::whereId($request->option_id)->first();
        $total_amount = $rate->total_charge + $insurance_amount;

        if (auth()->user()->user_type === 'individual' && auth()->user()->balance < $total_amount) {
            throw ValidationException::withMessages(['message' => 'Insufficient balance. Fund your wallet to continue']);
        }

        if (auth()->user()->user_type === 'business') {
            $current_balance = auth()->user()->balance;
            $current_overdraft_amount = $total_amount - $current_balance; // loan amount at the moment
            $overdraft_wallet = WalletOverdraft::where('user_id', auth()->user()->id)->first();
            $previous_overdraft = $overdraft_wallet->balance;
            $total_overdraft = $current_overdraft_amount + $previous_overdraft;
            if ($total_overdraft > auth()->user()->credit_limit) {
                throw ValidationException::withMessages(['message' => 'Insufficient balance: Order amount is above your credit limit']);
            }
        }

        $provider = $rate->provider_code;
        $book_aramex = $book_dhl = false;
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
                throw ValidationException::withMessages(['message' => 'We were not able to process your pickup. Please try again later']);
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

        if ($book_aramex || $book_dhl) {
            if (auth()->user()->user_type === 'business' && auth()->user()->balance < $total_amount) {
                $current_balance = auth()->user()->balance;
                $overdraft_amount = $total_amount - $current_balance;
                $overdraft_wallet = WalletOverdraft::where('user_id', auth()->user()->id)->first();
                $overdraft_wallet->balance += $overdraft_amount;
                $overdraft_wallet->save();

                WalletOverdraftLog::create([
                    'user_id' => auth()->user()->id,
                    'shipment_id' => $shipment->id,
                    'amount' => $overdraft_amount
                ]);
                auth()->user()->withdraw($current_balance);
            }

            if (auth()->user()->user_type === 'business' && auth()->user()->balance >= $total_amount)  auth()->user()->withdraw($total_amount);
            if (auth()->user()->user_type === 'individual') auth()->user()->withdraw($total_amount);


            $rate->insurance_option_id = $insurance->id;
            $rate->insurance_amount = $insurance_amount;
            $rate->save();

            $shipment->provider_id = $rate->courier_api_provider_id;
            $shipment->shipping_rate_log_id  = $rate->id;
            $shipment->provider = $provider;
            $shipment->is_paid = 1;
            $shipment->status = 'processing';
            $shipment->save();

            //fire email
            //Mail::to(auth()->user()->email)->send(new OrderConfirmation(['shipment' => $shipment,'shipment_item' => $shipment_item]));
        }

        if ($provider == 'dhl' && !$book_dhl) {
            return redirect(route('shipment.checkout', $request['shipment_id']))->with('error', 'DHL shipment is not available for selected locations at the moment. Please try again later');
        }

        if ($provider == 'aramex'  && !$book_aramex) {
            return redirect(route('shipment.checkout', $request['shipment_id']))->with('error', 'Aramex shipment is not available for selected locations at the moment. Please try again later');
        }

        return \redirect(route('shipment.details', $shipment->id));
    }

    private function bookWithAramex($bookShipmentRequest, $shipment, $shipment_item)
    {

    }

    /**
     * @throws ValidationException
     */
    public function trackShipment(TrackShipmentRequest $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $shipment_number = trim($request->number);
        $shipment = Shipment::where([
            'number' => $shipment_number,
            'user_id' => auth()->user()->id,
        ])->first();

        if (!$shipment) {
            throw ValidationException::withMessages(['number' => 'Tracking number not found']);
        }
        if ($shipment->provider == 'aramex') return $this->trackAramex($shipment);
        if ($shipment->provider == 'dhl') return $this->trackDhl($request, $shipment);
        return redirect()->back()->with('error', 'An error occurred. Please try again later');
    }

    private function trackAramex(Shipment $shipment): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
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

            return redirect()->back()->with('error', 'We are working on tracking info. Please check back later');
        } catch (\Throwable  $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Aramex Track Shipment'
                ])
                ->log($e->getMessage());
            return redirect()->back()->with('error', 'We are working on tracking info. Please check back later');
        }

    }

    private function trackDhl(TrackShipmentRequest $request, Shipment $shipment): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $dhl_shipment = DhlShipmentLog::where('shipment_id', $shipment->id)->first();
        if (!$dhl_shipment) return redirect()->back()->with('error', 'An error occurred. Please try again later');
        try {
            $dhl = new DHLServices($request);
            $response = $dhl->trackShipment($dhl_shipment);
            $tracking_data = json_decode($response, true);
            $tracking_log = false;
            foreach ($tracking_data['shipments'] as $td) {
                if (isset($td['events']) && count($td['events']) > 0) {
                    foreach ($td['events'] as $event) {
                        $check = TrackingLog::where([
                            'shipment_id' => $shipment->id,
                            'update_code' => $event['typeCode'],
                            'provider' => 'dhl',
                        ])->first();
                        if (!$check) {
                            $tracking_log = TrackingLog::create([
                                'shipment_id' => $shipment->id,
                                'update_code' => $event['typeCode'],
                                'waybill_number' => $td['pieces'][0]['trackingNumber'],
                                'update_description' => $event['description'],
                                'update_datetime' => $event['date'] . ' ' . $event['time'],
                                'update_location' => $event['serviceArea'][0]['description'],
                                'comment' => $td['description'],
                                'gross_weight' => $td['totalWeight'],
                                'chargeable_weight' => $td['totalWeight'],
                                'weight_unit' => 'metric',
                                'provider' => 'dhl',
                            ]);
                        }
                    }
                }
            }
            if ($tracking_log) return \redirect(route('shipment.track.details', $shipment->id));
            return redirect()->back()->with('message', 'We are working on tracking info. Please check back later');
        } catch (\Throwable $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Aramex Track Shipment'
                ])
                ->log($e->getMessage());
            return redirect()->back()->with('error', 'We are working on tracking info. Please check back later');
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
            try {
                $response = Http::withBasicAuth('parcelmartsNG', 'C^3zZ@4zJ!5iC#5m')
                    ->get('https://express.api.dhl.com/mydhlapi/test/address-validate', [
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

        return true;
    }
}
