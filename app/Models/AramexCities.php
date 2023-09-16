<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AramexCities extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'country_id', 'country_code', 'city_code'
    ];
}
