<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderProductTax extends Model
{
    use HasFactory;

    public function preorder_tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}
