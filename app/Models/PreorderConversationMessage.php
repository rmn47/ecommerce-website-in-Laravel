<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderConversationMessage extends Model
{
    use HasFactory;
    public function preorderConversationThread()
    {
        return $this->belongsTo(PreorderConversationThread::class);
    }
    
    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }
}
