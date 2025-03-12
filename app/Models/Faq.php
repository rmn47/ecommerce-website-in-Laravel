<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
    protected $with = ['faq_translations'];
    
    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $faq_translation = $this->faq_translations->where('lang', $lang)->first();
        return $faq_translation != null ? $faq_translation->$field : $this->$field;
    }

    public function faq_translations()
    {
        return $this->hasMany(FaqTranslation::class);
    }
}
