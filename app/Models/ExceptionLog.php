<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExceptionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name', 'line_number', 'error_message'
    ];
}
