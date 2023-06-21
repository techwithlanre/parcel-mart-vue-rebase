<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_id',
        'contact_name',
        'business_name',
        'contact_email',
        'contact_phone',
        'is_default',
    ];
}
