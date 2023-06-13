<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        return response()->json([
            'states'=>\App\Models\State::where('country_id', request()->country_id)->get()
        ]);
    }
}
