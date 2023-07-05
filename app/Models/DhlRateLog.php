<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DhlRateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'shipping_rate_log_id',
        'product_name',
        'product_code',
        'local_product_code',
        'network_type_code',
        'amount',
        'total_amount',
        'tax',
        'weight',
        'total_price_breakdown',
        'total_price',
        'detailed_price_breakdown',
        'pickup_capabilities',
        'delivery_capabilities',
        'pricing_date',
    ];
}
