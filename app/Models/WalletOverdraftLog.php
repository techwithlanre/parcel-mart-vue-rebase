<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletOverdraftLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','shipment_id','amount'
    ];
}
