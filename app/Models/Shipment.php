<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'origin_address',
        'destination_address',
        'status',
        'shipment_price',
        'shipment_paid_amount',
        'insurance_amount',
        'transaction_id',
        'insurance_id',
        'provider',
        'created_at',
        'updated_at',
    ];

    public function shipment_items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function shipping_rate_log(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ShippingRateLog::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function shipment_rate()
    {
        return $this->hasMany(ShippingRateLog::class);
    }
}
