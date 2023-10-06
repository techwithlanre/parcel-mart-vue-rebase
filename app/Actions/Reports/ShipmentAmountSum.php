<?php

namespace App\Actions\Reports;

use App\Models\ShippingRateLog;
use Illuminate\Http\Request;

class ShipmentAmountSum
{
    public function handle(Request $request, $sum, $user_id = null)
    {
        return ShippingRateLog::where(function ($query) use ($request) {
            if ($request->filled('from') && $request->filled('to')) {
                $query->whereBetween('shipping_rate_logs.created_at', [$request->from, $request->to]);
            }
        })->where(function ($query) use ($user_id) {
            if ($user_id != null) {
                $query->where('shipping_rate_logs.user_id', $user_id);
            }
        })->join('shipments', 'shipping_rate_logs.id', '=', 'shipments.shipping_rate_log_id')->sum($sum);
    }
}