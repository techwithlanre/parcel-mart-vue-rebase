<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipment_id',
        'product_name',
        'product_code',
        'local_product_code',
        'network_type_code',
        'courier_api_provider_id',
        'currency',
        'provider_amount_before_tax',
        'provider_tax',
        'provider_total_amount',
        'charge_before_tax',
        'charge_tax',
        'total_charge',
        'total_amount',
        'amount_before_tax',
        'tax',
        'created_at',
        'provider_code',
        'insurance_amount',
        'insurance_option_id',
        'pickup_number'
    ];

    public function courier_api_provider(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CourierApiProvider::class);
    }
}
