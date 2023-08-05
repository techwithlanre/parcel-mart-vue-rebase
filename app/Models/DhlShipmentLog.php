<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DhlShipmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'shipment_rate_log_id',
        'shipment_tracking_number',
        'tracking_url',
        'document_content',
        'package_details',
        'type_code',
        'cancel_pickup_url',
        'dispatch_confirmation_number',
    ];
}
