<?php

namespace App\Services;


use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Requests\TrackShipmentRequest;
use App\Models\AramexShipmentLog;
use App\Models\CourierApiProvider;
use App\Models\DhlRateLog;
use App\Models\DhlShipmentLog;
use App\Models\InsuranceOption;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use App\Models\TrackingLog;
use App\Models\WalletOverdraft;
use App\Models\WalletOverdraftLog;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Octw\Aramex\Aramex;

class ShipmentServices
{
    public function logShipment(CreateShipmentRequest $request)
    {
        $shipment = Shipment::create([
            'user_id' => auth()->user()->id,
            'origin_address' => json_encode($request->origin),
            'destination_address' => json_encode($request->destination),
            'status' => 'pending',
            'reference' => Str::uuid()
        ]);

        $shipment_item = ShipmentItem::create([
            'shipment_id' => $shipment->id,
            'item_category_id' => $request->shipment['category'],
            'description' => $request->shipment['description'],
            'quantity' => $request->shipment['quantity'],
            'weight' => $request->shipment['weight'],
            'height' => $request->shipment['height'],
            'length' => $request->shipment['length'],
            'width' => $request->shipment['width'],
            'value' => $request->shipment['value'],
        ]);

        return $shipment;
    }

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

    private function updateShipmentLog(Shipment $shipment, $response)
    {
        $shipment->provider_cost = $response->RateDetails->TotalAmountBeforeTax;
        $shipment->tax = $response->RateDetails->TaxAmount;
        $shipment->price = $response->TotalAmount->Value * 1.2;
        $shipment->save();
    }

    public function recalculateShipmentCost(CreateShipmentRequest $request): bool|\Illuminate\Http\RedirectResponse
    {
        //$shipment = $this->logRecalculateShipment($request);
        $shipment_id = request()->id;
        $shipment = Shipment::find($shipment_id);
        $check_aramex = CourierApiProvider::where('alias', 'aramex');
        $aramex_rate_found = $dhl_rate_found = false;
        if ($check_aramex->value('status') == 'active') {
            $aramex = new AramexServices($request);
            try {
                $response = $aramex->calculateShippingRate();
                if ($response) {
                    $shippingRateLog = ShippingRateLog::where([
                        'user_id' => auth()->user()->id,
                        'shipment_id'=>$shipment_id,
                        'courier_api_provider_id' => $check_aramex->value('id'),
                        'provider_code' => 'aramex',
                        ])->first();
                    $shippingRateLog->total_amount = $response->TotalAmount->Value;
                    $shippingRateLog->amount_before_tax = $response->RateDetails->TotalAmountBeforeTax;
                    $shippingRateLog->tax = $response->RateDetails->TaxAmount;
                    $shippingRateLog->save();
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
            } catch (\Exception $e) {
                //dd($e->getMessage());
                //return Redirect::back()->with('error', 'No shipment rate found for your package: ' . $e->getMessage());
                //return Redirect::back()->with('error', 'No shipment rate found for your package at the moment. Please try again.');
            }

        }

        $check_dhl = CourierApiProvider::where('alias', 'dhl');
        if ($check_dhl->value('status') == 'active') {
            $dhl = new DHLServices($request);
            try {
                $response = $dhl->calculateRate();
                $data = json_decode($response, true);
                if ($data) {
                    $products = $data['products'];
                    foreach ($products as $product) {
                        if ($product['totalPrice'][0]['price'] > 0) {
                            $shippingRateLog = ShippingRateLog::where([
                                //'user_id' => auth()->user()->id,
                                'shipment_id'=>$shipment_id,
                                'courier_api_provider_id' => $check_dhl->value('id'),
                                'provider_code' => 'dhl',
                                'product_code' => $product['productCode'],
                            ])->first();

                            if ($shippingRateLog)  {
                                $shippingRateLog->product_name = 'DHL - ' . $product['productName'];
                                $shippingRateLog->product_code = $product['productCode'];
                                $shippingRateLog->local_product_code = $product['localProductCode'];
                                $shippingRateLog->network_type_code = 'DHL - ' . $product['productName'];
                                $shippingRateLog->total_amount = number_format(($product['totalPrice'][0]['price'] * 0.925) + ($product['totalPrice'][0]['price'] * 0.075), 2);
                                $shippingRateLog->amount_before_tax = number_format($product['totalPrice'][0]['price'] * 0.925, 2);
                                $shippingRateLog->tax = number_format($product['totalPrice'][0]['price'] * 0.075, 2);
                                $shippingRateLog->save();

                                $dhlRateLog = DhlRateLog::where(['shipment_id' => $shipment->id,'product_code' => $product['productCode']])
                                    ->update([
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

                        }

                        $dhl_rate_found = true;
                    }
                }
            } catch (\Exception $e) {
                //return Redirect::back()->with('error', 'No shipment rate found for your package: ' . $e->getMessage());
            }
        }

        if (!$aramex_rate_found && !$dhl_rate_found) {
            return Redirect::back()->with('error', 'No shipment rate found for your package');
        }

        if ($aramex_rate_found || $dhl_rate_found) {
            $this->logRecalculateShipment($request);
            return Redirect::route('shipment.checkout', $shipment->id);
        }

        return Redirect::back()->with('error', 'No shipment rate found for your package');
    }

    public function calculateShipmentCost(CreateShipmentRequest $request): bool|\Illuminate\Http\RedirectResponse
    {
        $shipment = $this->logShipment($request);
        $check_aramex = CourierApiProvider::where('alias', 'aramex');
        $rate_found = false;
        if ($check_aramex->value('status') == 'active') {
            $aramex = new AramexServices($request);
            try {
                $response = $aramex->calculateShippingRate();
                if ($response) {
                    ShippingRateLog::create([
                        'user_id' => auth()->user()->id,
                        'shipment_id' => $shipment->id,
                        'product_name' => 'Aramex Shipping',
                        'courier_api_provider_id' => $check_aramex->value('id'),
                        'currency' => $response->TotalAmount->CurrencyCode,
                        'total_amount' => $response->TotalAmount->Value,
                        'amount_before_tax' => $response->RateDetails->TotalAmountBeforeTax,
                        'tax' => $response->RateDetails->TaxAmount,
                        'provider_code' => 'aramex',
                        'created_at' => now()
                    ]);

                    $shipment->has_rate = 1;
                    $shipment->save();
                    $rate_found = true;
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', 'No shipment rate found for your package: ' . $e->getMessage());
            }

        }

        $check_dhl = CourierApiProvider::where('alias', 'dhl');
        if ($check_dhl->value('status') == 'active') {
            $dhl = new DHLServices($request);
            try {
                $response = $dhl->calculateRate();
                $data = json_decode($response, true);
                if ($data) {
                    $products = $data['products'];
                    foreach ($products as $product) {
                        if ($product['totalPrice'][0]['price'] > 0) {
                            $rate_log = ShippingRateLog::create([
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
                        $rate_found = true;
                    }
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', 'No shipment rate found for your package: ' . $e->getMessage());
            }
        }

        if (!$rate_found) {
            $shipment->status = 'failed';
            $shipment->save();
        }

        return $rate_found ? Redirect::route('shipment.checkout', $shipment->id) : Redirect::back()->with('error', 'No shipment rate found for your package');
    }

    public function bookShipment(BookShipmentRequest $bookShipmentRequest)
    {
        $request = $bookShipmentRequest->validated();
        $shipment = Shipment::whereId($request['shipment_id'])->first();
        $shipment_item = ShipmentItem::where('shipment_id', $shipment->id)->first();
        $insurance = InsuranceOption::whereId($request['insurance'])->first();
        $insurance_amount = $insurance->amount;

        $rate = ShippingRateLog::whereId($request['option_id'])->first();
        $total_amount = str_replace(',', '', $rate->total_amount) + str_replace(',', '', $insurance_amount);

        if (auth()->user()->user_type === 'individual' && auth()->user()->balance < $total_amount) {
            return redirect(route('shipment.checkout', $request['shipment_id']))->with('error', 'Insufficient balance');
        }

        $provider = $rate->provider_code;
        $book_aramex = $book_dhl = false;
        if ($provider == 'aramex') {
            $aramex = new AramexServices($bookShipmentRequest);
            $book_aramex = $aramex->bookShipment($shipment, $shipment_item, $insurance, $rate);
        }

        if ($provider == 'dhl') {
            $dhl = new DHLServices($bookShipmentRequest);
            $book_dhl = $dhl->bookShipment($shipment, $shipment_item, $insurance, $rate, $bookShipmentRequest);
        }

        if ($book_aramex || $book_dhl) {
            if (auth()->user()->user_type === 'business' && auth()->user()->balance < $total_amount) {
                $current_balance = auth()->user()->balance;
                $overdraft_amount = $total_amount - $current_balance;
                $overdraft_wallet = WalletOverdraft::where('user_id', auth()->user()->id)->first();
                if (!$overdraft_wallet) {
                    WalletOverdraft::create([
                        'user_id' => auth()->user()->id,
                        'balance' => $overdraft_amount
                    ]);
                } else {
                    $overdraft_wallet->balance += $overdraft_amount;
                    $overdraft_wallet->save();
                }

                WalletOverdraftLog::create([
                    'user_id' => auth()->user()->id,
                    'shipment_id' => $shipment->id,
                    'amount' => $overdraft_amount
                ]);
                auth()->user()->withdraw($current_balance);
            }

            if (auth()->user()->user_type === 'business' && auth()->user()->balance >= $total_amount) {
                auth()->user()->withdraw($total_amount);
            }

            if (auth()->user()->user_type === 'individual') {
                auth()->user()->withdraw($total_amount);
            }

            $shipment->provider_id = $rate->courier_api_provider_id;
            $shipment->shipping_rate_log_id  = $rate->id;
            $shipment->provider = $provider;
            $shipment->number = random_int(1000000000, 9999999999);
            $shipment->status = 'processing';
            $shipment->save();
        }

        if (!$book_dhl && !$book_aramex) {
            return redirect(route('shipment.checkout', $request['shipment_id']))->with('error', 'Shipment booking failed at this time. Please try again later');
        }


        return \redirect(route('shipment.details', $shipment->id));
        //refund
        //book shipment with selected rate
    }

    private function bookWithAramex(BookShipmentRequest $bookShipmentRequest)
    {

    }

    public function trackShipment(TrackShipmentRequest $request)
    {
        $shipment_number = trim($request->number);
        $shipment = Shipment::where([
            'number' => $shipment_number,
            'user_id' => auth()->user()->id,
        ])->first();

        if (!$shipment) return \redirect(route('shipment.index'))->with('error', 'Tracking number not found');
        if ($shipment->provider == 'aramex') return $this->trackAramex($shipment);
        if ($shipment->provider == 'dhl') return $this->trackDhl($request, $shipment);
        return redirect()->back()->with('error', 'An error occurred. Please try again later');
    }

    private function trackAramex(Shipment $shipment)
    {
        $aramex_shipment = AramexShipmentLog::where('shipment_id', $shipment->id)->first();
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
    }

    private function trackDhl(TrackShipmentRequest $request, Shipment $shipment)
    {
        $dhl_shipment = DhlShipmentLog::where('shipment_id', $shipment->id)->first();
        if (!$dhl_shipment) return redirect()->back()->with('error', 'An error occurred. Please try again later');
        try {
            $dhl = new DHLServices($request);
            $response = $dhl->trackShipment($dhl_shipment);
            $tracking_data = json_decode($response, true);
            $tracking_log = false;
            foreach ($tracking_data['shipments'] as $td) {                //dd($td);
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
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', 'We are working on tracking info. Please check back later');
        }
    }
}
