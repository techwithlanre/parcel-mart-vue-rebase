<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Shipment;
use App\Models\User;
use App\Services\WalletServices;
use Bavix\Wallet\Models\Wallet;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $balance = number_format(auth()->user()->balance, 2);
        $totalUsersCount = User::count();
        $businessUsersCount = User::where('user_type', 'business')->count();
        $individualUsersCount = User::where('user_type', 'individual')->count();
        $countries = Country::all();
        $shipmentCount = !auth()->user()->is_admin
            ? Shipment::where('user_id', auth()->user()->id)->where('status', '!=', 'pending')->count()
            : Shipment::count();

        $shipments = Shipment::where('status', '!=', 'failed')->where('has_rate', 1)->with('shipment_rate')->latest()->take(10)->get();
        $log = [];
        foreach ($shipments as $shipment) {
            $origin = json_decode($shipment->origin_address, true);
            $destination = json_decode($shipment->destination_address, true);
            $log[] = [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'origin' => [
                    'name' => $origin['contact_name'],
                    'phone' => $origin['contact_phone'],
                    'email' => $origin['contact_email'],
                    'address_1' => $origin['address_1'],
                    'city' => getCity('id' , $origin['city'])->name,
                    'country' => getCountry('id' , $origin['country'])->name,
                ],
                'destination' => [
                    'name' => $destination['contact_name'],
                    'phone' => $destination['contact_phone'],
                    'email' => $destination['contact_email'],
                    'address_1' => $destination['address_1'],
                    'city' => getCity('id' , $destination['city'])->name,
                    'country' => getCountry('id' , $destination['country'])->name,
                ],
                'status' => $shipment->status
            ];
        }

        $totalWalletBalance = number_format(Wallet::sum('balance'), 2);
        return Inertia::render('Dashboard', compact(
            'balance', 'countries', 'log', 'shipmentCount',
            'totalUsersCount', 'businessUsersCount','individualUsersCount', 'totalWalletBalance'
        ));
    }
}
