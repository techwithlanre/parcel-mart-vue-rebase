<?php

use Illuminate\Support\Facades\Route;

Route::get('search-address', [\App\Http\Controllers\AddressBookController::class, 'searchAddressApi'])->name('search.address');
