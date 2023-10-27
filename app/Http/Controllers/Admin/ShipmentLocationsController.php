<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedShipmentCountry;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipmentLocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {

        $countries = Country::where(function ($query) use ($request) {
            $query->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('name', 'LIKE', '%'. $request->get('search'). '%');
            });
        })->orderBy('name', 'ASC')->paginate(10);
        $search = $request->get('search') ?? '';
        return Inertia::render('Admin/ShipmentLocations/Countries', compact('countries', 'search'));
    }

    public function states(Request $request, $country_id)
    {
        $states = State::where('country_id', $country_id)->orderBy('name', 'ASC')->paginate(10);
        return Inertia::render('Admin/ShipmentLocations/States', compact('states'));
    }

    public function cities(Request $request, $state_id)
    {
        $cities = City::where('state_id', $state_id)->orderBy('name', 'ASC')->paginate(10);
        $countries = Country::all();
        return Inertia::render('Admin/ShipmentLocations/Cities', compact('cities', 'countries', 'state_id'));
    }

    public function allowedDestinations()
    {
        $allowed_shipment_countries = AllowedShipmentCountry::paginate(10);
        $data = [];
        foreach ($allowed_shipment_countries as $xx) {
            $origin = getCountry('id', $xx['country_id'])->name;
            $allowed_destinations = explode(',', $xx['allowed_destinations']);
            $x = 0;
            $destination = [];
            foreach ($allowed_destinations as $yy) {
                $destination[] = [
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
        $countries = Country::all();
        $country = [];
        foreach($countries as $country) {
            $check = AllowedShipmentCountry::where('country_id', $country->id)->first();
            if(!$check) {
                $data = [
                    'country_id' => $country->id,
                    'allowed_destinations' => 161
                ];

                AllowedShipmentCountry::create($data);

                dump($data);
            }
        }

    
        //dd($country);







        exit;
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $allowed_countries = json_encode($request->allowed_countries);
        $allowed_shipment_countries = AllowedShipmentCountry::find($id);
        $allowed_shipment_countries->allowed_destinations = $allowed_countries;
        $allowed_shipment_countries->save();
        return redirect(route('shipment-locations.index'))->with('message', 'Saved');
    }

    public function storeCity(Request $request)
    {
        $state = getState('id', $request->state_id);
        $city = City::updateOrCreate(
            ['name' => $request->city_name, 'state_id' => $request->state_id],[
            'country_id' => $state->country_id,
            'country_code' => $state->country_code,
            'state_code' => $state->iso2
        ]);
        return to_route('cities', $request->state_id)->with('message', 'City created successfully');
    }
}
