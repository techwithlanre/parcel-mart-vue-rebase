<?php

namespace App\Http\Controllers\Mobile\General;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    //
    public function createAddress(Request $request){
        $user=  auth()->user();
        $address = new Address();
        $address->address = $request->address;
        $address->user_id = $user->id;
        $address->country_id = $request->country_id;
        $address->state_id = $request->state_id;
        $address->postcode = $request->postcode;
        $address->city = $request->city;
        $address->status = $request->status;
        $address->save();
    }

    public function ListAddresses(Request $request){
        $address = new Address();
       ;

    }

    public function userprofile(Request $request){
        $data=  auth()->user();
        return response(['data' =>  $data]);
    }

}
