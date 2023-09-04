<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsRateLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'shipment_id',
        'shipping_rate_log_id',
        'service_code',
        'packaging_type_code',
        'billing_weight',
        'total_price',
        'total_price_breakdown',
        'pricing_date',
    ];
}
