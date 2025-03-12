<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class WarrantyTranslation extends Model
{
  use PreventDemoModeChanges;

  protected $fillable = ['text', 'lang', 'warranty_id'];

  public function warranty(){
    return $this->belongsTo(Warranty::class);
  }
}
