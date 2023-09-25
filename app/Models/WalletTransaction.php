<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'transaction_id',
        'reference',
        'status',
        'amount',
        'after',
        'before',
        'comment',
        'description',
        'currency',
        'channel',
        'time_initiated'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
