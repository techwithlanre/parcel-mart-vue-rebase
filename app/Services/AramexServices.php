<?php

namespace App\Services;

use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Mail\OrderConfirmation;
use App\Models\AramexShipmentLog;
use App\Models\City;
use App\Models\Country;
use App\Models\InsuranceOption;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use Illuminate\Support\Facades\Mail;
use Octw\Aramex\Aramex;

class AramexServices
{
    public $request;
    public array $originAddressPayload = [];
    public array $destinationAddressPayload = [];
    public array $shipmentDetailsPayload = [];
    public string $originCountryCode = '';
    public string $destinationCountryCode = '';
    public string $userCountryCode = '';
    public string $shippingCurrency = '';

    public function __construct(CreateShipmentRequest | BookShipmentRequest $request)
    {
        $this->request = $request;
    }

    private function initializeCalculateRate(): void
    {
        $this->originCountryCode = getCountry('id', $this->request->origin['country'])->iso2;
        $this->originAddressPayload = [
            'line1' => $this->request->origin['address_1'],
            'line2' => $this->request->origin['address_2'],
            'city' => getCity('id', $this->request->origin['city'])->name,
            'country_code' => $this->originCountryCode
        ];

        $this->destinationCountryCode = getCountry('id', $this->request->destination['country'])->iso2;
        $this->destinationAddressPayload = [
            'line1' => $this->request->destination['address_1'],
            'line2' => $this->request->destination['address_2'],
            'city' => getCity('id', $this->request->destination['city'])->name,
            'country_code' => $this->destinationCountryCode
        ];


        $product_group = ($this->originCountryCode == $this->destinationCountryCode) ? 'DOM' : 'EXP';
        $product_type = ($this->originCountryCode == $this->destinationCountryCode) ? 'OND' : 'PPX';
        $this->shipmentDetailsPayload = [
            'weight' => $this->request->shipment['weight'], // KG
            'number_of_pieces' => $this->request->shipment['quantity'],
            'length'=>$this->request->shipment['length'],
            'width'=>$this->request->shipment['width'],
            'height'=>$this->request->shipment['height'],
            'payment_type' => 'P',
            'product_group' => $product_group,
            'product_type' => $product_type
        ];

        $this->setShippingCurrency();
    }

    public function calculateShippingRate()
    {
        $this->initializeCalculateRate();
        $response = Aramex::calculateRate(
            $this->originAddressPayload,
            $this->destinationAddressPayload,
            $this->shipmentDetailsPayload,
            $this->shippingCurrency
        );

        if (isset($response->error)) return false;
        return $response;
    }

    private function setShippingCurrency(): void
    {
        $this->shippingCurrency = (getCountry('id', auth()->user()->country_id)->iso2 == 'NG') ? 'NGN' : 'USD';
    }

    public function  bookShipment(Shipment $shipment, ShipmentItem $shipmentItem, InsuranceOption $insuranceOption, ShippingRateLog $shippingRateLog): bool
    {
        $origin = json_decode($shipment->origin_address, true);
        $destination = json_decode($shipment->origin_address, true);
        $product_group = (getCountry('id', $origin['country'])->iso2 == getCountry('id', $destination['country'])->iso2) ? 'DOM' : 'EXP';
        $product_type = (getCountry('id', $origin['country'])->iso2 == getCountry('id', $destination['country'])->iso2) ? 'OND' : 'PPX';
        $shipment_data = [
            'shipper' => [
                'name' => $origin['contact_name'],
                'email' => $origin['contact_email'],
                'phone'      => $origin['contact_phone'],
                'cell_phone' => $origin['contact_phone'],
                'country_code' => getCountry('id', $origin['country'])->iso2,
                'city' => getCity('id', $origin['city'])->name,
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
                'country_code' => getCountry('id', $destination['country'])->iso2,
                'city' => getCity('id', $destination['city'])->name,
                'zip_code' => $destination['postcode'],
                'line1' => $destination['address_1'],
                'line2' => !empty($destination['address_2']) ? $destination['address_2'] : $destination['landmark'],
                'line3' => $destination['landmark']
            ],
            'shipping_date_time' => time() + 50000, // shipping date
            'due_date' => time() + 60000,  // due date of the shipment
            'comments' => '', // ,comments
            'pickup_location' => 'at reception', // location as pickup
            'pickup_guid' => '', // GUID taken from createPickup method (optional)
            'weight' => $shipmentItem->weight, // weight
            'goods_country' => null, // optional
            'number_of_pieces' => $shipmentItem->quantity,  // number of items
            'description' => $shipment->description, // description
            'reference' => 'pm-'.time().str_shuffle(time()), // reference to print on shipment report (policy)
            //'shipper_reference' => '19191', // optional
            //'consignee_reference' => '010101', // optional
            //'services' => 'CODS,FIRST,FRDM', // ',' seperated string, refer to services in the official documentation
            //'cash_on_delivery_amount' => 10.32, // in case of CODS (in USD only "as they want")
            'insurance_amount' => $insuranceOption->amount, // optional
            //'collect_amount' => 0, // optional
            //'customs_value_amount' => 0, //optional (required for express shipping)
            //'cash_additional_amount' => 0, // optional
            //'cash_additional_amount_description' => 'Something here',
            'product_group' => $product_group, // or EXP (defined in config file, if you don't pass it will take the config value)
            'product_type' => $product_type, // refer to the official documentation (defined in config file, if you dont pass it will take the config value)
            'payment_type' => 'P', // P,C, 3 refer to the official documentation (defined in config file, if you dont pass it will take the config value)
            //'payment_option' => null, // refer to the official documentation (defined in config file, if you dont pass it will take the    value)
        ];

        $response = Aramex::createShipment($shipment_data);
        if (!empty($response->error)) {
            return false;
        }

        AramexShipmentLog::create([
            'shipment_id' => $shipment->id,
            'shipment_rate_log_id' => $shippingRateLog->id,
            'shipment_tracking_number' => $response->Shipments->ProcessedShipment->ID,
            'reference' => $response->Shipments->ProcessedShipment->Reference1,
            'label_url' => $response->Shipments->ProcessedShipment->ShipmentLabel->LabelURL,
            'label_content' => $response->Shipments->ProcessedShipment->ShipmentLabel->LabelFileContents,
            'details' => json_encode($response->Shipments->ProcessedShipment->ShipmentDetails)
        ]);
        //Mail::to(auth()->user()->email)->send(new OrderConfirmation($shipment_data));
        return true;
    }
}
