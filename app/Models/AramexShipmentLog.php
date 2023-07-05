<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AramexShipmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'shipment_rate_log_id',
        'aramex_id',
        'reference',
        'label_url',
        'label_content',
        'details'
    ];
}
