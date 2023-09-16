<?php

namespace App\Traits;

use App\Http\Requests\CreateShipmentOriginRequest;
use Octw\Aramex\Aramex;

trait ValidateAramexAddress
{
    /*public function __construct(protected CreateShipmentOriginRequest $request)
    {

    }*/

    public function index(CreateShipmentOriginRequest $request)
    {
        $response = Aramex::validateAddress([
            'line1' => 'Test', // optional (Passing it is recommended)
            'line2' => 'Test', // optional
            'line3' => 'Test', // optional
            'country_code' => 'JO',
            'postal_code' => '', // optional
            'city' => 'Amman',
        ]);
        dd($response);
    }
}