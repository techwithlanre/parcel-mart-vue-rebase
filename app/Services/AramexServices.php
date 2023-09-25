<?php

namespace App\Services;

use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Models\AramexShipmentLog;
use App\Models\InsuranceOption;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use Illuminate\Http\Request;use Illuminate\Support\Str;
use Nette\Schema\ValidationException;
use Octw\Aramex\Aramex;

class AramexServices
{
    public Request|BookShipmentRequest|CreateShipmentRequest | Shipment $request;
    public Shipment $shipment;
    public array $originAddressPayload = [];
    public array $destinationAddressPayload = [];
    public array $shipmentDetailsPayload = [];
    public string $originCountryCode = '';
    public string $destinationCountryCode = '';
    public string $shippingCurrency = '';

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
    }

    private function initializeCalculateRate(): void
    {
        $origin = ShipmentAddress::where([
            'shipment_id' => $this->shipment->id,
            'type' => 'origin',
        ])->first();

        $this->originCountryCode = getCountry('id', $origin->country_id)->iso2;
        $this->originAddressPayload = [
            'line1' => $origin->address_1,
            'line2' => $origin->landmark,
            'city' => getCity('id', $origin->city_id)->name,
            'country_code' => $this->originCountryCode
        ];

        $destination = ShipmentAddress::where([
            'shipment_id' => $this->shipment->id,
            'type' => 'destination',
        ])->first();

        $this->destinationCountryCode = getCountry('id', $destination->country_id)->iso2;
        $this->destinationAddressPayload = [
            'line1' => $destination->address_1,
            'line2' => $destination->landmark,
            'city' => getCity('id', $destination->city_id)->name,
            'country_code' => $this->destinationCountryCode,
        ];

        $product_type = $product_group = '';
        if ($this->originCountryCode == "NG") {
            if ($this->destinationCountryCode == "NG") {
                $product_group = 'DOM';
                $product_type = 'OND';
            }
            if ($this->destinationCountryCode != "NG") {
                $product_group = 'EXP';
                $product_type = 'PPX';
            }
        }

        if ($this->originCountryCode != "NG")  {
            if ($this->destinationCountryCode == "NG") {
                $product_group = 'EXP';
                $product_type = 'PPX';
            }
            if ($this->destinationCountryCode != "NG") {
                $product_group = 'EXP';
                $product_type = 'PPX';
            }
        }

        $item = ShipmentItem::where('shipment_id', $this->shipment->id)->first();
        $this->shipmentDetailsPayload = [
            'weight' => $item->weight, // KG
            'number_of_pieces' => $item->quantity,
            'length' => $item->length,
            'width'=> $item->width,
            'height'=>$item->height,
            'payment_type' => 'P',
            'product_group' => $product_group,
            'product_type' => $product_type
        ];

        $this->setShippingCurrency();
    }

    public function calculateShippingRate() {
        $this->initializeCalculateRate();
        $response = Aramex::calculateRate(
            $this->originAddressPayload,
            $this->destinationAddressPayload,
            $this->shipmentDetailsPayload,
            $this->shippingCurrency
        );

        //dd($response);

        if (isset($response->error)) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Aramex Recalculate Shipment Rate'
                ])
                ->log($response->errors->Notification->Message);
            return false;
        }

        return $response;
    }

    private function setShippingCurrency(): void
    {
        $this->shippingCurrency = 'NGN';
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function  bookShipment(BookShipmentRequest $request, ShipmentItem $shipmentItem, InsuranceOption $insuranceOption, ShippingRateLog $shippingRateLog, $pickup_guid = null): bool
    {
        $shipment = $this->shipment;
        $origin = ShipmentAddress::where([
            'shipment_id' => $this->shipment->id,
            'type' => 'origin',
        ])->first();;
        $destination = ShipmentAddress::where([
            'shipment_id' => $this->shipment->id,
            'type' => 'destination',
        ])->first();
        $product_group = (getCountry('id', $origin['country_id'])->iso2 == getCountry('id', $destination['country_id'])->iso2) ? 'DOM' : 'EXP';
        $product_type = (getCountry('id', $origin['country_id'])->iso2 == getCountry('id', $destination['country_id'])->iso2) ? 'OND' : 'PPX';

        $shipment_data = [
            'shipper' => [
                'name' => $origin['contact_name'],
                'email' => $origin['contact_email'],
                'phone'      => $origin['contact_phone'],
                'cell_phone' => $origin['contact_phone'],
                'country_code' => getCountry('id', $origin['country_id'])->iso2,
                'city' => getCity('id', $origin['city_id'])->name,
                'zip_code' => $origin['postcode'],
                'line1' => $origin['address_1'],
                'line2' => !empty($origin['address_2']) ? $origin['address_2'] : $origin['landmark'],
                'line3' => $origin['landmark'],
            ],
            'consignee' => [
                'name' => $destination['contact_name'],
                'email' => $destination['contact_email'],
                'phone'      => $destination['contact_phone'],
                'cell_phone' => $destination['contact_phone'],
                'country_code' => getCountry('id', $destination['country_id'])->iso2,
                'city' => getCity('id', $destination['city_id'])->name,
                'zip_code' => $destination['postcode'],
                'line1' => $destination['address_1'],
                'line2' => $destination['address_2'] ?? $destination['landmark'],
                'line3' => $destination['landmark']
            ],
            'shipping_date_time' => strtotime($request->shipment_date) + 50000, // shipping date
            'due_date' => strtotime($request->shipment_date) + 60000,  // due date of the shipment
            'comments' => '', // ,comments
            'pickup_location' => 'at reception', // location as pickup
            'pickup_guid' => $pickup_guid, // GUID taken from createPickup method (optional)
            'weight' => $shipmentItem->weight, // weight
            'goods_country' => null, // optional
            'number_of_pieces' => $shipmentItem->quantity,  // number of items
            'description' => $shipment->description, // description
            'reference' => 'pm-'.time().str_shuffle(time()), // reference to print on shipment report (policy)
            'shipper_reference' => 'shipper_' . Str::uuid(), // optional
            'consignee_reference' => 'shipper_' . Str::uuid(), // optional
            //'services' => 'CODS,FIRST,FRDM', // ',' seperated string, refer to services in the official documentation
            //'cash_on_delivery_amount' => 10.32, // in case of CODS (in USD only "as they want")
            'insurance_amount' => $insuranceOption->amount, // optional
            //'collect_amount' => 0, // optional
            'customs_value_amount' => (float) $shipmentItem->weight, //optional (required for express shipping) TODO
            //'cash_additional_amount' => 0, // optional
            //'cash_additional_amount_description' => 'Something here',
            'product_group' => $product_group, // or EXP (defined in config file, if you don't pass it will take the config value)
            'product_type' => $product_type, // refer to the official documentation (defined in config file, if you don't pass it will take the config value)
            'payment_type' => 'P', // P,C, 3 refer to the official documentation (defined in config file, if you don't pass it will take the config value)
            //'payment_option' => null, // refer to the official documentation (defined in config file, if you don't pass it will take the    value)
        ];

        $response = Aramex::createShipment($shipment_data); //TODO pickup date 4 days
        if (!empty($response->error)) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Aramex Create Shipment'
                ])
                ->log($response->errors[0]?->Message);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'message' => ['Can not process shipment with provider.'],
            ]);
        }

        AramexShipmentLog::create([
            'shipment_id' => $shipment->id,
            'shipment_rate_log_id' => $shippingRateLog->id,
            'shipment_tracking_number' => $response->Shipments->ProcessedShipment->ID,
            'aramex_id' => $response->Shipments->ProcessedShipment->ID,
            'reference' => $response->Shipments->ProcessedShipment->Reference1,
            'label_url' => $response->Shipments->ProcessedShipment->ShipmentLabel->LabelURL,
            'label_content' => $response->Shipments->ProcessedShipment->ShipmentLabel->LabelFileContents,
            'details' => json_encode($response->Shipments->ProcessedShipment->ShipmentDetails)
        ]);

        $shipment->number = $response->Shipments->ProcessedShipment->ID;
        $shipment->save();
        return true;
    }

    public function createPickup(BookShipmentRequest $bookShipmentRequest, ShipmentItem $shipment_item)
    {
        $shipment = $this->shipment;
        try {
            $origin = ShipmentAddress::where([
                'shipment_id' => $this->shipment->id,
                'type' => 'origin',
            ])->first();

            $volume = $shipment_item->height * $shipment_item->length * $shipment_item->width;
            return Aramex::createPickup([
                "name" => $origin['contact_name'], // User’s Name, Sent By or in the case of the consignee, to the Attention of.
                "cell_phone" => $origin['contact_phone'], // Phone Number
                "phone" => $origin['contact_phone'], // Phone Number
                "email" => $origin['contact_email'],
                "country_code" => getCountry('id', $origin['country_id'])->iso2, // ISO 3166-1 Alpha-2 Code
                "city" => getCity('id', $origin['city_id'])->name, // City Name
                "zip_code" => $origin['postcode'] ,// Postal Code
                "line1" => $origin['address_1'],
                "line2" => $origin['address_2'] ?? $origin['landmark'],
                "pickup_date" => strtotime($bookShipmentRequest->shipment_date), // time parameter describe the date of the pickup
                "ready_time" => strtotime($bookShipmentRequest->shipment_date),// time parameter describe the ready pickup date
                "last_pickup_time" => strtotime($bookShipmentRequest->shipment_date),// time parameter
                "closing_time" => strtotime($bookShipmentRequest->shipment_date),// time parameter
                "status" => 'Ready', // or Pending
                "pickup_location" => 'at reception', // location details
                "weight" => $shipment_item->weight,// wieght of the pickup (in KG)
                "volume" => $volume, // volume of the pickup  (in CM^3)
            ]);
        } catch (\Throwable $e) {
            //dd($e->getMessage());
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Aramex Create Pickup'
                ])
                ->log($e->getMessage());
            return false;
        }
    }
}
