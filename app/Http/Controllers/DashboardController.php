<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Shipment;
use App\Models\User;
use App\Services\WalletServices;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $balance = number_format(auth()->user()->balance, 2);
        $totalUsersCount = User::count();
        $countries = Country::all();
        $shipmentCount = Shipment::where('user_id', auth()->user()->id)->where('status', '!=', 'pending')->count();


        //$totalWalletBalance = WalletServices::all;

        return Inertia::render('Dashboard', compact('balance', 'countries', 'shipmentCount', 'totalUsersCount' ));
    }
}
