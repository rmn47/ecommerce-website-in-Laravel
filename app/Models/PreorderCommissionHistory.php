<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class PreorderCommissionHistory extends Model
{
    use PreventDemoModeChanges;

    public function preorder(){
        return $this->belongsTo(Preorder::class);
    }
}
