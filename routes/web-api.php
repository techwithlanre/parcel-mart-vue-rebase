<?php

use Illuminate\Support\Facades\Route;

Route::get('search-address', [\App\Http\Controllers\AddressBookController::class, 'searchAddressApi'])->name('search.address');
Route::get('allowed-countries/{country_id}', function (\Illuminate\Http\Request $request) {
    $allowed = \App\Models\AllowedShipmentCountry::where('country_id', $request->country_id)->first();
    if ($allowed) {
        $data = [];
        foreach (explode(',', $allowed->allowed_destinations) as $dest) {
            $data[] = [
                'id' => $dest,
                'name' => getCountry('id', $dest)->name
            ];
        }
        return response()->json($data);
    }

    return response()->json([]);

});

Route::get('country/{id}', function (\Illuminate\Http\Request $request) {
    return \App\Models\Country::find($request->id);
});

Route::get('state/{id}', function (\Illuminate\Http\Request $request) {
    return \App\Models\State::find($request->id);
});

Route::get('city/{id}', function (\Illuminate\Http\Request $request) {
    return \App\Models\City::find($request->id);
});