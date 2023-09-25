<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_1',
        'address_2',
        'user_id',
        'country_id',
        'state_id',
        'postcode',
        'city_id',
        'landmark',
        'status',
        'shipment_id'
    ];

    public function address_contacts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AddressContact::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
