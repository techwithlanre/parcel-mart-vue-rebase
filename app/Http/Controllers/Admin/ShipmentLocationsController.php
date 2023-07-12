<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedShipmentCountry;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipmentLocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allowed_shipment_countries = AllowedShipmentCountry::paginate(10);
        $data = [];
        foreach ($allowed_shipment_countries as $xx) {
            $origin = getCountry('id', $xx['country_id'])->name;
            $allowed_destinations = explode(',', $xx['allowed_destinations']);
            $destinations = [];
            $x = 0;
            foreach ($allowed_destinations as $yy) {
                $destination[$x++] = [
                    'id' => $yy,
                    'country' => getCountry('id', $yy)->name
                ];

            }
            $data[] = [
                'origin' => [
                    'id' => $xx['country_id'],
                    'country' => $origin
                ],
                'destination_countries' => $destination
            ];
        }

        $countries = Country::all();

        return Inertia::render('Admin/ShipmentLocations/ShipmentLocations', compact('data', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $allowed_countries = implode(',', $request->allowed_countries);
        AllowedShipmentCountry::create([
            'country_id' => $request->origin,
            'allowed_destinations' => $allowed_countries
        ]);

        //return redirect(route('shipment-locations.index'))->with('message', 'Saved');
    }

    public function show(string $id)
    {

    }

    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $allowed_countries = json_encode($request->allowed_countries);
        $allowed_shipment_countries = AllowedShipmentCountry::find($id);
        $allowed_shipment_countries->allowed_destinations = $allowed_countries;
        $allowed_shipment_countries->save();
        return redirect(route('shipment-locations.index'))->with('message', 'Saved');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
