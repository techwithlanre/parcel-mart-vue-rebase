<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShipmentRequest;
use App\Models\Address;
use App\Models\Country;
use App\Models\InsuranceOption;
use App\Models\ItemCategory;
use App\Models\Shipment;
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
        $countries = Country::all();
        $categories = ItemCategory::all();
        return Inertia::render('Shipments/Create', compact('addresses', 'countries', 'categories'));
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

    public function testCalculateShipment(CreateShipmentRequest $request, ShipmentServices $shipmentServices)
    {
        return $shipmentServices->calculateShipmentCost($request);
    }

    public function initialize(Request $request)
    {
        dd($request);
    }

    public function checkout($id)
    {
        $shipment = Shipment::whereId($id)->with('shipment_items')->first();
        $origin = json_decode($shipment->origin_address);
        $destination = json_decode($shipment->destination_address);
        $insurance_options = InsuranceOption::all();
        return Inertia::render('Shipments/Checkout', compact('shipment', 'origin', 'destination','insurance_options'));
    }
}
