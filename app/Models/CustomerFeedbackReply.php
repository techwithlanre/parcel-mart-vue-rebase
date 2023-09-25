<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFeedbackReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_name',
        'feedback_email',
        'feedback_subject',
        'feedback_message',
        'user_id',
        'customer_feedback_id',
        'status',
        'feedback_phone',
        'submitted_on',
        'feedback_file'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer_feedback(): BelongsTo
    {
        return $this->belongsTo(CustomerFeedback::class)->orderBy('id', 'desc');
    }
}