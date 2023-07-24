<?php

namespace App\Http\Controllers\Auth;

use App\Events\BusinessRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BusinessRegisterRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredBusinessController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(BusinessRegisterRequest $request): RedirectResponse
    {
        $request = $request->validated();
        $user = User::create([
            'business_name' => $request['business_name'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'user_type' => 'business',
            'country_id' => $request['country'],
            'credit_limit' => '30000'
        ]);

        event(new Registered($user));
        event(new BusinessRegistered($user));


        Auth::login($user);
        return redirect(RouteServiceProvider::HOME);
    }
}
