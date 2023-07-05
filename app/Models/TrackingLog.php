<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'waybill_number',
        'update_code',
        'update_description',
        'update_datetime',
        'update_location',
        'comment',
        'problem_code',
        'gross_weight',
        'chargeable_weight',
        'weight_unit',
        'provider',
    ];
}
