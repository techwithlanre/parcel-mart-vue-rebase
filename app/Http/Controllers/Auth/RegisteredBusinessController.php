<?php

namespace App\Http\Controllers\Auth;

use App\Events\BusinessRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BusinessRegisterRequest;
use App\Models\ReferralLog;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredBusinessController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(BusinessRegisterRequest $businessRegisterRequest): RedirectResponse
    {
        $request = $businessRegisterRequest->validated();
        $ref_by = $businessRegisterRequest->has('ref_by') ? User::where('ref_code', $request['ref_by'])->value('id') : NULL;

        $user = User::create([
            'business_name' => $request['business_name'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'user_type' => 'business',
            'country_id' => $request['country'],
            'credit_limit' => 0,
            'ref_code' => Str::lower(Str::random(8)),
            'ref_by_id' => $ref_by,
            'dob' => $request['dob'],
            'gender' => $request['gender'],
        ]);

        if ($ref_by != NULL) {
            $this->createReferral($user, $ref_by);
        }

        event(new Registered($user));
        event(new BusinessRegistered($user));


        Auth::login($user);
        return redirect(RouteServiceProvider::HOME);
    }

    private function createReferral(User $user, $ref_by): void
    {
        ReferralLog::create([
            'user_id' => $user->id,
            'referred_by_id' => $ref_by,
            'is_paid' => 0,
        ]);
    }
}
