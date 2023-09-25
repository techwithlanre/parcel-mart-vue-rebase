<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\ShippingRateLog;
use App\Models\User;
use Bavix\Wallet\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalyticController extends Controller
{
    public function index(): \Inertia\Response
    {
        $monthlySales = collect(DB::table('shipping_rate_logs')
            ->join('shipments', 'shipping_rate_logs.id', '=', 'shipments.shipping_rate_log_id')
            ->select(DB::raw('YEAR(shipping_rate_logs.created_at) as year, MONTH(shipping_rate_logs.created_at) as month'),
                DB::raw('SUM(total_charge) as total_sales'))
            ->groupBy(DB::raw('YEAR(shipping_rate_logs.created_at)'), DB::raw('MONTH(shipping_rate_logs.created_at)'))->get());

        $monthlySales = $monthlySales->map(function ($item) {
            return [
              'month' => date('F', mktime(0, 0, 0, $item->month, 10)),
              'year' => $item->year,
              'total_sales' => $item->total_sales
            ];
        });

        $monthlyShipments = collect(DB::table('shipments')->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as shipment_count')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')->whereIn('status', ['processing', 'delivered'])->get());

        $monthlyShipments = $monthlyShipments->map(function ($item) {
            return [
                'month' => date('F', mktime(0, 0, 0, $item->month, 10)),
                'year' => $item->year,
                'shipment_count' => $item->shipment_count
            ];
        });

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
        return Inertia::render('Admin/Analytics/Analytics', compact('monthlySales', 'monthlyShipments'
        ));
    }

    public function users(): \Inertia\Response
    {
        $userRetention = $this->userRetentionChartData();
        $balance = number_format(auth()->user()->balance, 2);
        $totalUsersCount = User::count();
        $businessUsersCount = User::where(['user_type' => 'business'])->count();
        $individualUsersCount = User::where(['user_type' => 'individual'])->count();
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
        return Inertia::render('Admin/Analytics/Analytics', compact(
            'balance', 'countries', 'log', 'shipmentCount',
            'totalUsersCount', 'businessUsersCount','individualUsersCount', 'totalWalletBalance', 'userRetention'
        ));
    }

    public function shipments(Request $request): \Inertia\Response
    {
        $shipmentCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->count();

        $shipments = Shipment::where('status', '!=', 'failed')->where('has_rate', 1)->where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->with('shipment_rate')->latest()->take(5)->get();
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

        $totalShipmentCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->count();
        $processingShipmentsCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->where('status', 'processing')->count();
        $pendingShipmentsCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->where('status', 'pending')->count();
        $deliveredShipmentsCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->where('status', 'delivered')->count();
        $shipmentCost = ShippingRateLog::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->sum('provider_total_amount');
        $shipmentCharge = ShippingRateLog::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->sum('total_charge');
        $insuranceAmount = ShippingRateLog::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->sum('insurance_amount');

        return Inertia::render('Admin/Analytics/ShipmentsAnalytics', compact('log', 'shipmentCount', 'totalShipmentCount', 'processingShipmentsCount', 'pendingShipmentsCount',
            'deliveredShipmentsCount', 'shipmentCost', 'shipmentCharge', 'insuranceAmount'
        ));
    }

    public function payments(Request $request): \Inertia\Response
    {
        $shipmentCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->count();

        $shipments = Shipment::where('status', '!=', 'failed')->where('has_rate', 1)->where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->with('shipment_rate')->latest()->take(5)->get();
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

        $totalShipmentCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->count();
        $processingShipmentsCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->where('status', 'processing')->count();
        $pendingShipmentsCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->where('status', 'pending')->count();
        $deliveredShipmentsCount = Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->where('status', 'delivered')->count();
        $shipmentCost = ShippingRateLog::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->sum('provider_total_amount');
        $shipmentCharge = ShippingRateLog::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->sum('total_charge');
        $insuranceAmount = ShippingRateLog::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->sum('insurance_amount');

        return Inertia::render('Admin/Analytics/ShipmentsAnalytics', compact('log', 'shipmentCount', 'totalShipmentCount', 'processingShipmentsCount', 'pendingShipmentsCount',
            'deliveredShipmentsCount', 'shipmentCost', 'shipmentCharge', 'insuranceAmount'
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
