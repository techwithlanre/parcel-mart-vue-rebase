<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedShipmentCountry extends Model
{
    use HasFactory;

    protected $fillable = [
        "country_id",
        "allowed_destinations"
    ];
}
