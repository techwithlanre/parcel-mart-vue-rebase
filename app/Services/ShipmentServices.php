<?php

namespace App\Services;


use App\Http\Requests\CreateShipmentRequest;
use App\Models\Country;
use Octw\Aramex\Aramex;

class ShipmentServices
{
    public function calculateShipmentCost(CreateShipmentRequest $request)
    {
        $originAddress = [
            'line1' => '',
            'city' => 'Kano',
            'country_code' => 'NG'
        ];

        $destinationAddress = [
            'line1' => '',
            'city' => 'Ikeja',
            'country_code' => 'NG'
        ];


        $shipmentDetails = [
            'weight' => 5, // KG
            'number_of_pieces' => 2,
        ];

        $currency = 'NGN';
        $data = Aramex::calculateRate($originAddress, $destinationAddress , $shipmentDetails , $currency);
        dd($data);
    }
}
