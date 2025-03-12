<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Note extends Model
{
    use PreventDemoModeChanges;
    protected $with = ['note_translations'];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $note_translation = $this->note_translations->where('lang', $lang)->first();
        return $note_translation != null ? $note_translation->$field : $this->$field;
    }
    
    public function note_translations()
    {
        return $this->hasMany(NoteTranslation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
