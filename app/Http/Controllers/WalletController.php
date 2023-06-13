<?php

namespace App\Http\Controllers;

use Bavix\Wallet\Models\Wallet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallet = auth()->user()->wallet;
        dd($wallet);
        return Inertia::render('Wallet/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function pay()
    {
        //
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
