<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayInitializeRequest;
use App\Services\PaystackServices;
use App\Services\WalletServices;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Unicodeveloper\Paystack\Paystack;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $balance = number_format(auth()->user()->balance, 2);;
        return Inertia::render('Wallet/Index', compact('balance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function initialize(PayInitializeRequest $request, WalletServices $services, PaystackServices $paystackServices)
    {
        return $services->initializePay($request, $paystackServices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
