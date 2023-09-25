<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Models\Country;
use App\Models\ReferralLog;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {

        return Inertia::render('Auth/Register', [
            'countries'=>Country::all()
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserRegisterRequest $userRegisterRequest): RedirectResponse
    {
        $request = $userRegisterRequest->validated();
        $ref_by = $userRegisterRequest->filled('ref_by')
            ? User::where('ref_code', $request['ref_by'])->value('id')
            : NULL;

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country_id' => $request['country'],
            'password' => Hash::make($request['password']),
            'user_type' => 'individual',
            'ref_code' => Str::lower(Str::random(8)),
            'ref_by_id' => $ref_by,
            'dob' => $request['dob'],
            'gender' => $request['gender'],
        ]);

        if ($ref_by != NULL) {
            ReferralLog::create([
                'user_id' => $user->id,
                'referred_by_id' => $ref_by,
                'is_paid' => 0,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);
        return redirect(RouteServiceProvider::HOME);
    }
}
