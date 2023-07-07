<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Shipment;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $balance = number_format(auth()->user()->balance, 2);
        $countries = Country::all();
        $shipmentCount = Shipment::where('user_id', auth()->user()->id)->where('status', '!=', 'pending')->count();
        return Inertia::render('Dashboard', compact('balance', 'countries', 'shipmentCount'));
    }
}
