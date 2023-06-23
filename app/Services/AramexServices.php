<?php

namespace App\Services;

use App\Http\Requests\CreateShipmentRequest;
use App\Models\City;
use App\Models\Country;
use Octw\Aramex\Aramex;

class AramexServices
{
    public CreateShipmentRequest $request;
    public array $originAddressPayload = [];
    public array $destinationAddressPayload = [];
    public array $shipmentDetailsPayload = [];
    public string $originCountryCode = '';
    public string $destinationCountryCode = '';
    public string $userCountryCode = '';
    public string $shippingCurrency = '';

    public function __construct(CreateShipmentRequest $request)
    {
        $this->request = $request;
        $this->initialize();
    }

    private function initialize(): void
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
            'product_type' => $product_type,
        ];
        $this->setShippingCurrency();
    }

    public function calculateShippingRate(CreateShipmentRequest $request)
    {
        $response = Aramex::calculateRate(
            $this->originAddressPayload,
            $this->destinationAddressPayload ,
            $this->shipmentDetailsPayload ,
            $this->shippingCurrency
        );

        if (isset($response->error)) {

            return false;
        }

        return $response;
    }

    private function setShippingCurrency(): void
    {
        $this->shippingCurrency = (getCountry('id', auth()->user()->country_id)->iso2 == 'NG') ? 'NGN' : 'USD';
    }
}
