<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = request();
        $log = [];
        $shipments = Shipment::with('shipping_rate_log')->where('user_id', auth()->user()->id)->where('has_rate', 1)
            ->where('status', '!=', 'failed')->where(function ($query) use ($filter) {
                $query->when($filter->filled('status'), function ($query) use ($filter) {
                    return $filter->get('status') !== 'all'
                        ? $query->where('status', $filter->get('status'))
                        : $query;
                });
            })->orderBy('id', 'desc')->paginate(10);

        foreach ($shipments as $shipment) {
            $origin = ShipmentAddress::where(['shipment_id'=>$shipment->id, 'type' => 'origin'])->first();
            $destination = ShipmentAddress::where(['shipment_id'=>$shipment->id, 'type' => 'destination'])->first();
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
        return Inertia::render('Admin/Shipments/Index', compact(
            'log', 'shipments'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function filterShipment(Request $request): \Illuminate\Http\JsonResponse
    {
        $log = [];
        $shipments = Shipment::where('has_rate', 1)
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
                'rate' => $shipment->shipping_rate_log,
                'user' => User::find($shipment->user_id),
            ];
        }

        return response()->json($log);
    }

    public function export(Request $request): \Illuminate\Http\JsonResponse
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
                'rate' => $shipment->shipping_rate_log,
            ];
        }


        return response()->json($log);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
