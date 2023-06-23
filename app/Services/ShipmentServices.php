<?php

namespace App\Services;


use App\Http\Requests\CreateShipmentRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\CourierApiProvider;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use Illuminate\Support\Facades\Redirect;
use Octw\Aramex\Aramex;

class ShipmentServices
{
    public function logShipment(CreateShipmentRequest $request)
    {
        $shipment = Shipment::create([
            'origin_address' => json_encode($request->origin),
            'destination_address' => json_encode($request->destination),
            'status' => 'pending'
        ]);

        $shipment_item = ShipmentItem::create([
            'shipment_id' => $shipment->id,
            'item_category_id' => $request->shipment['category'],
            'description' => $request->shipment['description'],
            'quantity' => $request->shipment['quantity'],
            'weight' => $request->shipment['weight'],
            'height' => $request->shipment['height'],
            'length' => $request->shipment['length'],
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
        $check_aramex = CourierApiProvider::where('alias', 'aramex')->value('status');
        if ($check_aramex == 'active') {
            $aramex = new AramexServices($request);
            $response = $aramex->calculateShippingRate($request);
            dd($response);
            if ($response) {
                dd($response);
            }
        }

        //$this->updateShipmentLog($shipment, $response);
        return Redirect::route('shipment.checkout', $shipment->id);
    }

    public function createShipment(CreateShipmentRequest $request): void
    {
        $callResponse = Aramex::createShipment([
            'shipper' => [
                'name' => $request->origin['contact_name'],
                'email' => 'email@users.companies',
                'phone'      => '+123456789982',
                'cell_phone' => '+321654987789',
                'country_code' => 'NG',
                'city' => City::whereId($request->origin['city'])->value('name'),
                'zip_code' => '',
                'line1' => 'Line1 Details',
                'line2' => 'Line2 Details',
                'line3' => 'Line3 Details',
            ],
            'consignee' => [
                'name' => $request->destination['contact_name'],
                'email' => 'email@users.companies',
                'phone'      => '+123456789982',
                'cell_phone' => '+321654987789',
                'country_code' => 'NG',
                'city' => City::whereId($request->destination['city'])->value('name'),
                'zip_code' => '',
                'line1' => 'Line1 Details',
                'line2' => 'Line2 Details',
                'line3' => 'Line3 Details',
            ],
            'shipping_date_time' => time() + 50000,
            'due_date' => time() + 60000,
            'comments' => 'No Comment',
            'pickup_location' => 'at reception',
            'pickup_guid' => '',
            'weight' => 1,
            'number_of_pieces' => $request->shipment['quantity'],
            'description' => 'Goods Description, like Boxes of flowers',
        ]);
    }
}
