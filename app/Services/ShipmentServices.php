<?php

namespace App\Services;


use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Requests\TrackShipmentRequest;
use App\Models\AramexShipmentLog;
use App\Models\CourierApiProvider;
use App\Models\DhlRateLog;
use App\Models\InsuranceOption;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use App\Models\TrackingLog;
use App\Models\WalletOverdraft;
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

    private function updateShipmentLog(Shipment $shipment, $response)
    {
        $shipment->provider_cost = $response->RateDetails->TotalAmountBeforeTax;
        $shipment->tax = $response->RateDetails->TaxAmount;
        $shipment->price = $response->TotalAmount->Value * 1.2;
        $shipment->save();
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
                dd($e);
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
                        $rate_found = true;
                    }
                }
            } catch (\Exception $e) {
                dd($e);
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
        $total_amount = floatval($rate->total_amount) + floatval($insurance_amount);

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
            $book_dhl = $dhl->bookShipment($shipment, $shipment_item, $insurance, $rate);
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
                auth()->user()->withdraw($current_balance);
            }
            if (auth()->user()->user_type === 'business' && auth()->user()->balance < $total_amount) {
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
        $shipment = Shipment::where('number', $shipment_number)->first();

        if (!$shipment) return \redirect(route('shipment.index'))->with('error', 'Tracking number not found');

        if ($shipment->provider == 'aramex') {
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
        }


    }
}
