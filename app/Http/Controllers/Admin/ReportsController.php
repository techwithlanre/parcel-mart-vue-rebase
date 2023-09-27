<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Reports\ShipmentAmountSum;
use App\Actions\Reports\ShipmentCost;
use App\Actions\Reports\ShipmentCount;
use App\Actions\Reports\ShipmentStatusCount;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\ShippingRateLog;
use App\Models\User;
use App\Models\WalletTransaction;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function shipmentsReport(Request $request)
    {
        $shipmentCountAction = new ShipmentCount();
        $totalShipmentCount = $shipmentCountAction->handle($request);

        $shipmentStatusCount = new ShipmentStatusCount();
        $processingShipmentsCount = $shipmentStatusCount->handle($request, 'processing');
        $pendingShipmentsCount = $shipmentStatusCount->handle($request, 'pending');
        $deliveredShipmentsCount = $shipmentStatusCount->handle($request, 'delivered');

        $shipmentAmountSum = new ShipmentAmountSum();
        $shipmentCost = $shipmentAmountSum->handle($request, 'provider_total_amount');
        $shipmentCharge = $shipmentAmountSum->handle($request, 'total_charge');
        $insuranceAmount = $shipmentAmountSum->handle($request, 'insurance_amount');

        $shipments = $this->shipmentQuery($request);

        $log = [];
        foreach ($shipments as $shipment) {
            $log[] = [
                'shipment' =>$shipment,
                'origin' => $this->shipmentOrigin($shipment),
                'destination' => $this->shipmentDestination($shipment),
            ];
        }

        return Inertia::render('Admin/Reports/ShipmentsReport', compact(
            'totalShipmentCount', 'processingShipmentsCount', 'pendingShipmentsCount',
            'deliveredShipmentsCount', 'shipmentCost', 'shipmentCharge', 'insuranceAmount', 'log', 'shipments'
        ));
    }

    private function shipmentOrigin(Shipment $shipment): array
    {
        $origin = ShipmentAddress::where(['shipment_id'=>$shipment->id,'type'=>'origin'])->first();
        return [
            'name' => $origin?->contact_name,
            'phone' => $origin?->contact_phone,
            'email' => $origin?->contact_email,
            'address_1' => $origin?->address_1,
            'city' => getCity('id' , $origin?->city_id)?->name,
            'country' => getCountry('id' , $origin?->country_id)?->name,
        ];
    }

    private function shipmentDestination(Shipment $shipment): array
    {
        $destination = ShipmentAddress::where(['shipment_id'=>$shipment->id,'type'=>'destination'])->first();
        return [
            'name' => $destination?->contact_name,
            'phone' => $destination?->contact_phone,
            'email' => $destination?->contact_email,
            'address_1' => $destination?->address_1,
            'city' => getCity('id' , $destination?->city_id)?->name,
            'country' => getCountry('id' , $destination?->country_id)?->name,
        ];
    }

    private function shipmentQuery(Request $request)
    {
        return Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('number'), function ($query) use ($request) {
                $query->where('number', 'LIKE', '%'. $request->get('number'). '%');
            });
        })->where(function ($query) use ($request) {
            $query->when($request->filled('status'), function ($query) use ($request) {
                if ($request->get('status') !== 'all')
                    $query->where('status', $request->get('status'));
            });
        })->where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->with('user','shipping_rate_log')->orderBy('id', 'desc')->paginate(10);
    }

    public function usersReport()
    {
        $totalUsersCount = User::count();
        $businessUsersCount = User::where('user_type', 'business')->count();
        $individualUsersCount = User::where('user_type', 'individual')->count();

        return Inertia::render('Admin/Reports/UserReport', compact('totalUsersCount', 'businessUsersCount', 'individualUsersCount'));
    }

    public function paymentReport(Request $request): \Inertia\Response
    {
        $transactions = WalletTransaction::with(['user' => function($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'phone');
        }])->where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->latest()->paginate(10);

        $shipmentAmountSum = new ShipmentAmountSum();
        $shipmentCost = $shipmentAmountSum->handle($request, 'provider_total_amount');
        $shipmentRevenue = $shipmentAmountSum->handle($request, 'total_charge');
        $shipmentProfit = $shipmentRevenue - $shipmentCost;
        $insuranceAmount = $shipmentAmountSum->handle($request, 'insurance_amount');
        $walletBalance = Wallet::sum('balance');

        $depositsCount = WalletTransaction::where(['description' =>'deposit', 'status' => 'success'])
            ->where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->count();

        $paymentsCount = WalletTransaction::where(['description' =>'payment', 'status' => 'success'])
            ->where(function ($query) use ($request) {
                $query->when($request->filled('from'), function ($query) use ($request) {
                    $query->when($request->filled('to'), function ($query) use ($request) {
                        return $query->whereBetween('created_at', [$request->from, $request->to]);
                    });
                });
            })->count();

        $totalDeposits = WalletTransaction::where(['description' =>'deposit', 'status' => 'success'])
            ->where(function ($query) use ($request) {
                $query->when($request->filled('from'), function ($query) use ($request) {
                    $query->when($request->filled('to'), function ($query) use ($request) {
                        return $query->whereBetween('created_at', [$request->from, $request->to]);
                    });
                });
            })->sum('amount');
        $dateRangeUrl = $request->all();
        return Inertia::render('Admin/Reports/PaymentReport', compact('dateRangeUrl','walletBalance','totalDeposits','paymentsCount','transactions', 'shipmentCost', 'shipmentRevenue', 'insuranceAmount', 'shipmentProfit', 'depositsCount'));
    }

    public function taxReport()
    {
        
    }

    private function shipmentsList(Request $request): \Illuminate\Http\JsonResponse
    {
        $log = [];
        $shipments = Shipment::where(function ($query) use ($request) {
                $query->when($request->filled('number'), function ($query) use ($request) {
                    return $query->where('number', 'LIKE', '%'. $request->get('number'). '%');
                });
            })->where(function ($query) use ($request) {
                $query->when($request->filled('status'), function ($query) use ($request) {
                    return $request->get('status') !== 'all'
                        ? $query->where('status', $request->get('status'))
                        : $query;
                });
            })->with('shipment_rate')->orderBy('id', 'desc')->paginate(10);

        foreach ($shipments as $shipment) {
            $origin = ShipmentAddress::where([
                'shipment_id'=>$shipment->id,
                'type'=>'origin'
            ])->first();

            $destination = ShipmentAddress::where([
                'shipment_id'=>$shipment->id,
                'type'=>'destination'
            ])->first();
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
                'status' => $shipment->status,
                'rate' => $shipment->shipping_rate_log
            ];
        }

        return response()->json($log);
    }
}
