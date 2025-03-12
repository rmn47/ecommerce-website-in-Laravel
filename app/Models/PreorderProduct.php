<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderProduct extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['preorder_product_translations'];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $preorder_product_translation = $this->preorder_product_translations->where('lang', $lang)->first();
        return $preorder_product_translation != null ? $preorder_product_translation->$field : $this->$field;
    }

    public function preorder_product_translations()
    {
        return $this->hasMany(PreorderProductTranslation::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function main_category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'preorder_product_categories');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function product_queries()
    {
        return $this->hasMany(ProductQuery::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preorder_prepayment()
    {
        return $this->belongsTo(PreorderPrepayment::class);
    }

    public function preorder_sample_order()
    {
        return $this->belongsTo(PreorderSampleOrder::class);
    }

    public function preorder_coupon()
    {
        return $this->belongsTo(PreorderCoupon::class);
    }

    public function preorder_refund()
    {
        return $this->hasOne(PreorderRefund::class);
    }

    public function preorder_cod()
    {
        return $this->hasOne(PreorderCashondelivery::class);
    }

    public function preorder_discount()
    {
        return $this->belongsTo(PreorderDiscount::class);
    }

    public function preorder_shipping()
    {
        return $this->hasOne(PreorderShipping::class);
    }

    public function preorder_stock()
    {
        return $this->belongsTo(PreorderStock::class);
    }

    public function preorder_product_taxes()
    {
        return $this->hasMany(PreorderProductTax::class)->with('preorder_tax');
    }

    public function preorder_discount_periods()
    {
        return $this->hasMany(PreorderDiscountPeriod::class);
    }

    public function preorder_wholesale_prices()
    {
        return $this->hasMany(PreorderWholesalePrice::class);
    }
    
    public function preorder()
    {
        return $this->hasMany(Preorder::class,'product_id');
    }

    public function preorderProductQueries()
    {
        return $this->hasMany(PreorderProductQuery::class);
    }

    public function preorderProductreviews()
    {
        return $this->hasMany(PreorderProductReview::class);
    }

    public function taxes()
    {
        return $this->hasMany(PreorderProductTax::class)->with('preorder_tax');
    }

    public function preorderConversations()
    {
        return $this->hasMany(PreorderConversationThread::class);

    }
}
