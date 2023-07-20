<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAddressRequest;
use App\Models\Address;
use App\Models\Country;
use App\Services\AddressServices;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddressBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::where('user_id', auth()->user()->id)
            ->with('address_contacts', 'country', 'city')->paginate(10);
        $countries = Country::all();
        return Inertia::render('AddressBook/Index', compact('addresses', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all();
        return Inertia::render('AddressBook/Create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAddressRequest $request, AddressServices $services)
    {
        return $services->storeAddress($request);
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
        $address = Address::where('user_id', auth()->user()->id)
            ->whereId($id)
            ->with('address_contacts', 'country', 'city')->first();
        $countries = Country::all();
        return Inertia::render('AddressBook/Edit', compact('address', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateAddressRequest $request, string $id, AddressServices $services)
    {
        return $services->updateAddress($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function searchAddressApi(Request $request)
    {
        $search = Address::where(function ($query) use ($request) {
            return $query->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('address', 'LIKE', "%{$request->search}%")
                    ->orWhere('landmark', 'LIKE', "%{$request->search}%")
                    ->orWhere('postcode', 'LIKE', "%{$request->search}%");
            });
        })->with('address_contacts', 'country', 'city')->get();

        return response()->json([
            'data' => $search
        ]);
    }
}
