<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderProductTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['preorder_product_id', 'product_name', 'unit', 'description', 'lang'];

    public function preorderProduct(){
      return $this->belongsTo(PreorderProduct::class);
    }
}
