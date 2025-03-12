<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class PreorderProductQuery extends Model
{
    use HasFactory,PreventDemoModeChanges;

    public function preorderProduct(){
        return  $this->belongsTo(PreorderProduct::class);
      }
  
      public function user(){
         return $this->belongsTo(User::class,'customer_id');
      }
}
