<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFeedbackReply extends Model
{
    use HasFactory;

    protected $fillable = ['feedback_name','feedback_email','feedback_subject','feedback_message','user_id', 'customer_feedback_id','status','feedback_phone','submitted_on','feedback_file'];

    public function user() {
    
        return $this->belongsTo(User::class);
    }

    public function customer_feedback() {
    
        return $this->belongsTo(CustomerFeedback::class)->orderBy('id', 'desc');
    }
    
}
