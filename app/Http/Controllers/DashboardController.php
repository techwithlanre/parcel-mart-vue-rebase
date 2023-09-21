<?php

namespace App\Http\Controllers;

use App\Meta\Meta;
use App\Models\Country;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\User;
use App\Services\WalletServices;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(): \Inertia\Response
    {
        $balance = number_format(auth()->user()->balance, 2);
        $countries = Country::all();
        $shipmentCount = !auth()->user()->is_admin
            ? Shipment::where('user_id', auth()->user()->id)->where('status', '!=', 'pending')->count()
            : Shipment::count();

        $shipments = Shipment::where('status', '!=', 'failed')->where('user_id', auth()->user()->id)
                ->with('shipment_rate')->latest()->take(5)->get();
        $log = [];
        foreach ($shipments as $shipment) {
            $origin = ShipmentAddress::where(['shipment_id'=>$shipment->id, 'type'=>'origin'])->first();
            $destination = ShipmentAddress::where(['shipment_id'=>$shipment->id, 'type'=>'destination'])->first();
            $log[] = [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'origin' => [
                    'name' => $origin['contact_name'],
                    'phone' => $origin['contact_phone'],
                    'email' => $origin['contact_email'],
                    'address_1' => $origin['address_1'],
                    'city' => getCity('id' , $origin['city_id'])->name,
                    'country' => getCountry('id' , $origin['country_id'])->name,
                ],
                'destination' => [
                    'name' => $destination['contact_name'],
                    'phone' => $destination['contact_phone'],
                    'email' => $destination['contact_email'],
                    'address_1' => $destination['address_1'],
                    'city' => getCity('id' , $destination['city_id'])->name,
                    'country' => getCountry('id' , $destination['country_id'])->name,
                ],
                'status' => $shipment->status
            ];
        }

        $totalWalletBalance = number_format(Wallet::sum('balance'), 2);
        return Inertia::render('Dashboard', compact(
            'balance', 'countries', 'log', 'shipmentCount', 'totalWalletBalance'
        ));
    }
}
