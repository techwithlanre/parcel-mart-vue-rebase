<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentAddress extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'shipment_id',
        'contact_name',
        'contact_phone',
        'contact_email',
        'business_name',
        'address_1',
        'landmark',
        'address_2',
        'country_id',
        'state_id',
        'city_id',
        'postcode',
        'type',
    ];
}
