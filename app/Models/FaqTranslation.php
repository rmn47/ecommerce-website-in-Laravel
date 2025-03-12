<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['faq_id', 'question','answer', 'lang'];
    public function faq(){
        return $this->belongsTo(Faq::class);
      }
}
