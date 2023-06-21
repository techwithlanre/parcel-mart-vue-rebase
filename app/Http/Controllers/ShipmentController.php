<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\ShipmentServices;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Shipments/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $addresses = Address::where('user_id', auth()->user()->id)
            ->with('address_contacts', 'country', 'city')->get();
        return Inertia::render('Shipments/Create', compact('addresses'));
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

    public function testCalculateShipment(ShipmentServices $shipmentServices)
    {
        return $shipmentServices->calculateShipmentCost();
    }

    public function initialize()
    {

    }
}
