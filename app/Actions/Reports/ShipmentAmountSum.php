<?php

namespace App\Actions\Reports;

use App\Models\ShippingRateLog;
use Illuminate\Http\Request;

class ShipmentAmountSum
{
    public function handle(Request $request, $sum)
    {
        return ShippingRateLog::where(function ($query) use ($request) {
            $query->when($request->filled('from'), function ($query) use ($request) {
                $query->when($request->filled('to'), function ($query) use ($request) {
                    return $query->whereBetween('shipping_rate_logs.created_at', [$request->from, $request->to]);
                });
            });
        })->join('shipments', 'shipping_rate_logs.id', '=', 'shipments.shipping_rate_log_id')->sum($sum);
    }
}