<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'address_2',
        'user_id',
        'country_id',
        'state_id',
        'postcode',
        'city_id',
        'landmark',
        'status'
    ];

    public function address_contacts()
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
