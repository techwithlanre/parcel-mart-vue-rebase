<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'origin_country_id',
        'origin_state_id',
        'origin_city_id',
        'destination_country_id',
        'destination_state_id',
        'destination_city_id',
        'quantity',
        'weight',
        'length',
        'width',
        'height',
        'commercial_invoice',
        'parking_list',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function origin_country(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Country::class, 'id', 'origin_country_id');
    }

    public function origin_state(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(State::class, 'id', 'origin_state_id');
    }

    public function origin_city(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(City::class, 'id', 'origin_city_id');
    }

    public function destination_country(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Country::class, 'id', 'destination_country_id');
    }

    public function destination_state(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(State::class, 'id', 'destination_state_id');
    }

    public function destination_city(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(City::class, 'id', 'destination_city_id');
    }
}
