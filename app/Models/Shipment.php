<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'pickup_number',
        'provider',
        'provider_id',
        'shipping_rate_log_id',
        'has_rate',
        'status',
        'is_paid',
        'created_at',
        'updated_at',
        'reference'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipment_items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function shipping_rate_log(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ShippingRateLog::class);
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

    public function origin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ShipmentAddress::class)->where('type', 'origin');
    }

    public function destination(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ShipmentAddress::class)->where('type', 'destination');
    }

    public function selected_shipment_rate()
    {
        return $this->hasMany(ShippingRateLog::class)->where('id', $this->shipping_rate_log_id);
    }
}
