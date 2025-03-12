<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderConversationThread extends Model
{
    use HasFactory;
    protected $fillable = ['preorder_product_id', 'sender_id', 'receiver_id'];

    public function preorderProduct()
    {
        return $this->belongsTo(PreorderProduct::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function messages()
    {
        return $this->hasMany(PreorderConversationMessage::class);
    }
}
