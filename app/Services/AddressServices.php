<?php

namespace App\Services;

use App\Http\Controllers\AddressBookController;
use App\Http\Requests\CreateAddressRequest;
use App\Models\Address;
use App\Models\AddressContact;

class AddressServices
{
    public function storeAddress(CreateAddressRequest $request)
    {
        $data = $request->validated();
        $address = Address::create([
            'user_id'=>auth()->user()->id,
            'address' => $request->address,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city,
            'landmark' => $request->landmark,
            'postcode' => $request->postcode,
        ]);

        $address_contact = AddressContact::create([
            'address_id' => $address->id,
            'contact_name' => $request->contact_name,
            'business_name' => $request->business_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'is_default' => $address->address_contacts()->count() > 0 ? 0 : 1,
        ]);

        sleep(1);

        return redirect()->route('address-book.index')->with('message', 'Address created successfully');
    }

    public function updateAddress(CreateAddressRequest $request, $id)
    {
        $data = $request->validated();
        $address = Address::find($id);
        $address->address = $request->address;
        $address->country_id = $request->country_id;
        $address->state_id = $request->state_id;
        $address->city_id = $request->city_id;
        $address->landmark = $request->landmark;
        $address->postcode = $request->postcode;
        $address->save();

        $address_contact = AddressContact::where(['address_id' => $address->id])->first();
        $address_contact->contact_name = $request->contact_name;
        $address_contact->business_name = $request->business_name;
        $address_contact->contact_email = $request->contact_email;
        $address_contact->contact_phone = $request->contact_phone;
        $address_contact->save();
        sleep(1);

        return redirect()->route('address-book.index')->with('message', 'Address updated successfully');
    }
}
