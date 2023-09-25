<?php

namespace App\Actions\Reports;

use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentCount
{
    public function handle(Request $request)
    {
        return Shipment::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->from, $request->to]);
                });
            });
        })->count();
    }
}