<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\ShippingRateLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function shipmentsReport()
    {
        $totalShipmentCount = Shipment::all()->count();
        $processingShipmentsCount = Shipment::where('status', 'processing')->count();
        $pendingShipmentsCount = Shipment::where('status', 'pending')->count();
        $deliveredShipmentsCount = Shipment::where('status', 'delivered')->count();
        $shipmentCost = ShippingRateLog::sum('provider_total_amount');
        $shipmentCharge = ShippingRateLog::sum('total_charge');
        $insuranceAmount = ShippingRateLog::sum('insurance_amount');
        return Inertia::render('Admin/Reports/ShipmentsReport', compact(
            'totalShipmentCount', 'processingShipmentsCount', 'pendingShipmentsCount',
            'deliveredShipmentsCount', 'shipmentCost', 'shipmentCharge', 'insuranceAmount'
        ));
    }

    public function usersReport()
    {
        
    }

    public function paymentReport(): \Inertia\Response
    {
        return Inertia::render('Admin/Reports/PaymentReport');
    }

    public function taxReport()
    {
        
    }

    private function shipmentsList(Request $request): \Illuminate\Http\JsonResponse
    {
        $log = [];
        $shipments = Shipment::where('user_id', auth()->user()->id)->where('has_rate', 1)
            ->where(function ($query) use ($request) {
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
