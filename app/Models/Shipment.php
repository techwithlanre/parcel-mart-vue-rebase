<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
