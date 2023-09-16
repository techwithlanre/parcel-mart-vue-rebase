<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        $userRetention = $this->userRetentionChartData();
        $balance = number_format(auth()->user()->balance, 2);
        $totalUsersCount = User::count();
        $businessUsersCount = User::where('user_type', 'business')->count();
        $individualUsersCount = User::where('user_type', 'individual')->count();
        $countries = Country::all();
        $shipmentCount = Shipment::count();
        $shipments = Shipment::where('status', '!=', 'failed')->where('has_rate', 1)->with('shipment_rate')->latest()->take(5)->get();
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
        return Inertia::render('Admin/Dashboard/Dashboard', compact(
            'balance', 'countries', 'log', 'shipmentCount',
            'totalUsersCount', 'businessUsersCount','individualUsersCount', 'totalWalletBalance', 'userRetention'
        ));
    }

    private function userRetentionChartData(): array
    {
        $days = [7, 14, 30, 60, 180, 365];
        $userRetention = [];
        foreach ($days as $day) {
            Artisan::call("calculate:retention-rate {$day}");
            $userRetention[] = [
                'day' => $day,
                'percentage' => trim(Artisan::output())
            ];
        }

        return $userRetention;
    }
}
