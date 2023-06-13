<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'phone' => 'required|numeric',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        $data['password'] = bcrypt($request->password);
        $data['business_name'] = $request->business_name;
        $data['currency_id'] = $request->currency_id;
        $user = User::create($data);
        $token = $user->createToken('API Token')->accessToken;
        return response([ 'user' => $user, 'token' => $token]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Incorrect Details.
            Please try again']);
        }
        $token = auth()->user()->createToken('API Token')->accessToken;
        return response(['user' => auth()->user(), 'token' => $token]);
    }
    public function country()
    {
        $countries =Country::all();
        return response(['country' =>$countries]);
    }
    public function states($country_id)
    {
        $states =State::whereCountry_id($country_id)->get();
        return response(['states' =>$states]);
    }
    public function city($state_id)
    {
        $countries =City::whereState_id($state_id)->get();
        return response(['cities' =>$countries]);
    }
}
