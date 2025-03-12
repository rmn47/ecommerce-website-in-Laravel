<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class NoteTranslation extends Model
{
    use PreventDemoModeChanges;
    protected $fillable = ['name', 'lang', 'note_id'];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }
}
