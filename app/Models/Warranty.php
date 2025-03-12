<?php

namespace App\Models;

use App;
use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use PreventDemoModeChanges;
    protected $with = ['warranty_translations'];
    
    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $warranty_translation = $this->warranty_translations->where('lang', $lang)->first();
        return $warranty_translation != null ? $warranty_translation->$field : $this->$field;
    }

    public function warranty_translations()
    {
        return $this->hasMany(WarrantyTranslation::class);
    }
}
