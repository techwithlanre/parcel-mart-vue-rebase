<?php

namespace App\Actions\Reports;

use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentCount
{
    public function handle(Request $request, $user_id = null)
    {
        return Shipment::where(function ($query) use ($request) {
            if ($request->filled('from') && $request->filled('to')) {
                $query->whereBetween('created_at', [$request->from, $request->to]);
            }
        })->where(function ($query) use ($user_id) {
            if ($user_id != null) {
                $query->where('user_id', $user_id);
            }
        })->count();
    }
}