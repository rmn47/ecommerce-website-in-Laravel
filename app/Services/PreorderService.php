<?php

namespace App\Services;

use App\Models\Preorder;
use App\Models\PreorderCashondelivery;
use App\Models\PreorderCommissionHistory;
use App\Models\PreorderCoupon;
use App\Models\PreorderDiscount;
use App\Models\PreorderPrepayment;
use App\Models\PreorderProduct;
use App\Models\PreorderProductTax;
use App\Models\PreorderProductTranslation;
use App\Models\PreorderRefund;
use App\Models\PreorderShipping;
use App\Models\PreorderStock;
use App\Utility\PreorderNotificationUtility;
use Illuminate\Http\Request;
use Str;
use Carbon\Carbon;

class PreorderService
{   
    // Preorder Product Store
    public function productStore(Request $request){
        $authUser = auth()->user();
        $discount_start_date        = null;
        $discount_end_date          = null;

        $slug = Str::slug($request->product_name);
        $same_slug_count = PreorderProduct::where('product_slug', 'LIKE', $slug . '%')->count();
        $slug_suffix = $same_slug_count ? '-' . $same_slug_count + 1 : '';
        $slug .= $slug_suffix;

        if ($request['date_range'] != null) {
            $date_var            = explode(" to ", $request['date_range']);
            $discount_start_date = strtotime($date_var[0]) ?? Carbon::now()->timestamp;
            $discount_end_date   = strtotime($date_var[1]) ?? Carbon::now()->timestamp;
        }

        $tags = [];

        if ($request['meta_title'] == null) {
            $request['meta_title'] = $request['name'];
        }
        if ($request['meta_description'] == null) {
            $request['meta_description'] = strip_tags($request['description']);
        }

        if ($request['meta_img'] == null) {
            $request['meta_img'] = $request['thumbnail'];
        }

        $product                                = new PreorderProduct();
        $product->product_name                  = $request->product_name;
        $product->product_slug                  = $slug;
        $product->category_id                   = $request->category_id;
        $product->user_id                       = $authUser->user_type == 'seller' ? $authUser->id : get_admin()->id;
        $product->brand_id                      = $request->brand_id;
        $product->unit                          = $request->unit;
        $product->weight                        = $request->weight;
        $product->min_qty                       = $request->min_qty;
        $product->tags                          = json_encode($tags);
        $product->barcode                       = $request->barcode;
        $product->thumbnail                     = $request->thumbnail;
        $product->images                        = $request->images;
        $product->video_provider                = $request->video_provider;
        $product->video_link                    = $request->video_link;
        $product->description                   = $request->description;
        $product->price_type                    = $request->price_type;
        $product->unit_price                    = $request->unit_price;
        $product->meta_title                    = $request->meta_title;
        $product->meta_description              = $request->meta_description;
        $product->meta_image                    = $request->meta_image;
        if ($request->is_published != null) {
            $product->is_published              = $request->is_published;
        }
        if ($request->is_featured != null) {
            $product->is_featured               = $request->is_featured;
        }

        if ($request->is_show_on_homepage != null) {
            $product->is_show_on_homepage       = $request->is_show_on_homepage;
        }
        $product->available_date                = $request->available_date;
        $product->is_available                  = $request->is_available;
        $product->campaign                      = $request->campaign;
        $product->frequently_bought_type        = $request->frequently_bought_selection_type;
        $product->frequently_bought_product     = json_encode($request->fq_bought_preorder_product_ids);
        $product->more_products                 = json_encode($request->pre_order_product_ids);
        $product->frequently_bought_category    = $request->fq_bought_product_category_id;
        $product->discount_start_date           = $discount_start_date;
        $product->discount_end_date             = $discount_end_date;
        $product->discount_type                 = $request->discount_type;
        $product->discount                      = $request->discount;
        $product->add_wholesale_price           = $request->add_wholesale_price ? 1 : 0;
        $product->show_lead_time                = $request->show_lead_time ? 1 : 0;
        $product->is_prepayment                 = $request->is_prepayment ? 1 : 0;
        $product->is_sample_order               = $request->is_sample_order ? 1 : 0;
        $product->is_coupon                     = $request->is_coupon ? 1 : 0;
        $product->is_Advance_discount           = $request->is_Advance_discount ? 1 : 0;
        $product->is_refundable                 = $request->is_refundable ? 1 : 0;
        $product->is_cod                        = $request->is_cod ? 1 : 0;
        $product->is_stock_visibility           = $request->is_stock_visibility ? 1 : 0;

        $product->save();

        //Product categories
        $product->categories()->attach($request->category_ids);

        // Product Tax table 
        foreach ($request['tax_id'] as $key => $val) {
            $product_tax = new PreorderProductTax();
            $product_tax->tax_id = $val;
            $product_tax->preorder_product_id = $product->id;
            $product_tax->tax = $request['tax_amount'][$key];
            $product_tax->tax_type = $request['tax_type'][$key];
            $product_tax->save();


            $product->preorder_product_tax_id   = $product_tax->id;
            $product->save();
        }

        // prepayment
        if ($request->is_prepayment) {
            $prepayment = new PreorderPrepayment();
            $prepayment->preorder_product_id = $product->id;
            $prepayment->prepayment_type = $request->prepayment_type;
            $prepayment->prepayment_amount = $request->prepayment_amount;
            $prepayment->save();

            $product->preorder_prepayment_id   = $prepayment->id;
            $product->save();
        }

        // Coupon
        if ($request->is_coupon) {
            $coupon_start_date = null;
            $coupon_end_date   = null;
            if ($request['coupon_date_range'] != null) {
                $date_var               = explode(" to ", $request['coupon_date_range']);
                $coupon_start_date = strtotime($date_var[0]);
                $coupon_end_date   = strtotime($date_var[1]);
            }

            $coupoon = new PreorderCoupon();
            $coupoon->preorder_product_id = $product->id;
            $coupoon->coupon_code = $request->coupon_code;
            $coupoon->coupon_amount = $request->coupon_amount;
            $coupoon->coupon_type = $request->coupon_type;
            $coupoon->coupon_benefits = json_encode($request->coupon_benefits);
            $coupoon->coupon_instructions = $request->coupon_instructions;
            $coupoon->coupon_start_date = $coupon_start_date;
            $coupoon->coupon_end_date = $coupon_end_date;
            $coupoon->save();

            $product->preorder_coupon_id   = $coupoon->id;
            $product->save();
        }

        // Discount
        if ($request->is_Advance_discount) {
            $discount = new PreorderDiscount();
            $discount->preorder_product_id = $product->id;
            $discount->after_preorder_discount_type = $request->after_preorder_discount_type;
            $discount->after_preorder_discount_amount = $request->after_preorder_discount_amount;
            $discount->direct_purchase_discount_type = $request->direct_purchase_discount_type;
            $discount->direct_purchase_discount_amount = $request->direct_purchase_discount_amount;
            $discount->save();

            $product->preorder_discount_id   = $discount->id;
            $product->save();
        }

        // Refund
        if ($request->is_refundable) {
            $refund = new PreorderRefund();
            $refund->preorder_product_id = $product->id;
            $refund->show_refund_note = $request->show_refund_note;
            $refund->note_id = $request->refund_note_id;
            $refund->save();

            $product->preorder_refund_id   = $refund->id;
            $product->save();
        }

        // Shipping
        $shipping = new PreorderShipping();
        $shipping->preorder_product_id = $product->id;
        $shipping->shipping_type = $request->shipping_type;
        $shipping->shipping_time = $request->shipping_time;
        $shipping->show_shipping_time = $request->show_shipping_time;
        $shipping->min_shipping_days = $request->min_shipping_days;
        $shipping->max_shipping_days = $request->max_shipping_days;
        $shipping->show_shipping_note = $request->show_shipping_note;
        $shipping->note_id = $request->shipping_note_id;
        $shipping->save();

        $product->preorder_shipping_id   = $shipping->id;
        $product->save();

        // cash on deliver (COD)
        if ($request->is_cod) {
            $cod = new PreorderCashondelivery();
            $cod->preorder_product_id = $product->id;
            $cod->prepayment_needed = $request->prepayment_needed;
            $cod->show_cod_note = $request->show_cod_note;
            $cod->note_id = $request->delivery_note_id;
            $cod->save();

            $product->preorder_cashondelivery_id   = $cod->id;
            $product->save();
        }

        // stcok
        if ($request->is_stock_visibility) {
            $stock = new PreorderStock();
            $stock->preorder_product_id = $product->id;
            $stock->stock_visibility_state = $request->stock_visibility_state;
            $stock->current_stock = $request->current_stock;
            $stock->low_stock_stock = $request->low_stock_stock;
            $stock->is_low_stock_warning = $request->is_low_stock_warning;
            $stock->is_custom_order_show = $request->is_custom_order_show;
            $stock->preorder_quantity = $request->preorder_quantity;
            $stock->final_order_quantity = $request->final_order_quantity;
            $stock->save();

            $product->preorder_stock_id   = $stock->id;
            $product->save();
        }

        if($request->button == "unpublish"){
             $product->is_published = 0;
             $product->save();
        }

        $product_translation = PreorderProductTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'preorder_product_id' => $product->id]);
        $product_translation->product_name = $request->product_name;
        $product_translation->unit         = $request->unit;
        $product_translation->description  = $request->description;
        $product_translation->save();
    }

    // Preorder Product update
    public function productUpdate(Request $request, $preorderProduct){
        // dd($request->all());
        $discount_start_date = null;
        $discount_end_date   = null;
        if ($request['date_range'] != null) {
            $date_var               = explode(" to ", $request['date_range']);
            $discount_start_date = strtotime($date_var[0]) ?? Carbon::now()->timestamp;
            $discount_end_date   = strtotime($date_var[1]) ?? Carbon::now()->timestamp;
        }


        $tags = [];
        if ($request['tags'][0] != null) {
            foreach (json_decode($request['tags'][0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }

        if ($request['meta_title'] == null) {
            $request['meta_title'] = $request['name'];
        }
        if ($request['meta_description'] == null) {
            $request['meta_description'] = strip_tags($request['description']);
        }

        if ($request['meta_img'] == null) {
            $request['meta_img'] = $request['thumbnail'];
        }

        $product = $preorderProduct;

        if($request->lang == env("DEFAULT_LANGUAGE")){
            $product->product_name = $request->product_name;
            $product->unit         = $request->unit;
            $product->description  = $request->description;
        }
        
        $product->category_id                   = $request->category_id;
        $product->brand_id                      = $request->brand_id;
        $product->weight                        = $request->weight;
        $product->min_qty                       = $request->min_qty;
        $product->tags                          = json_encode($tags);
        $product->barcode                       = $request->barcode;
        $product->thumbnail                     = $request->thumbnail;
        $product->images                        = $request->images;
        $product->video_provider                = $request->video_provider;
        $product->video_link                    = $request->video_link;
        $product->price_type                    = $request->price_type;
        $product->unit_price                    = $request->unit_price;
        $product->meta_title                    = $request->meta_title;
        $product->meta_description              = $request->meta_description;
        $product->meta_image                    = $request->meta_image;


        $product->is_published                  = $request->is_published;
        $product->is_featured                  = $request->is_featured;
        $product->is_available                  = $request->is_available;
        $product->available_date                = $request->available_date;
        $product->campaign                      = $request->campaign;
        $product->frequently_bought_type        = $request->frequently_bought_selection_type;
        $product->frequently_bought_product     = json_encode($request->fq_bought_product_ids);
        $product->more_products                 = json_encode($request->pre_order_product_ids);
        $product->frequently_bought_category    = $request->frequently_bought_category;
        $product->discount_start_date           = $discount_start_date;
        $product->discount_end_date             = $discount_end_date;
        $product->discount_type                 = $request->discount_type;
        $product->discount                      = $request->discount;
        $product->add_wholesale_price           = $request->add_wholesale_price ? 1 : 0;
        $product->show_lead_time                = $request->show_lead_time ? 1 : 0;
        $product->is_prepayment                 = $request->is_prepayment ? 1 : 0;
        $product->is_sample_order               = $request->is_sample_order ? 1 : 0;
        $product->is_coupon                     = $request->is_coupon ? 1 : 0;
        $product->is_Advance_discount           = $request->is_Advance_discount ? 1 : 0;
        $product->is_refundable                 = $request->is_refundable ? 1 : 0;
        $product->is_cod                        = $request->is_cod ? 1 : 0;
        $product->is_stock_visibility           = $request->is_stock_visibility ? 1 : 0;

        $product->save();

        //Product categories
        $product->categories()->sync($request->category_ids);

        // Product Tax table 
        $existingTaxes = $product->preorder_product_taxes;
        foreach ($request['tax_id'] as $key => $taxId) {
            $productTax = $existingTaxes->firstWhere('tax_id', $taxId);
            if ($productTax) {
                $productTax->tax = $request['tax_amount'][$key];
                $productTax->tax_type = $request['tax_type'][$key];
                $productTax->save();
            } else {
                // If it doesn't exist, create a new tax record
                $productTax = new PreorderProductTax();
                $productTax->tax_id = $taxId;
                $productTax->preorder_product_id = $product->id;
                $productTax->tax = $request['tax_amount'][$key];
                $productTax->tax_type = $request['tax_type'][$key];
                $productTax->save();
            }
        }

        // prepayment
        if ($request->is_prepayment) {
            $prepayment = $preorderProduct->preorder_prepayment ?? new PreorderPrepayment();
            $prepayment->preorder_product_id = $product->id;
            $prepayment->prepayment_type = $request->prepayment_type;
            $prepayment->prepayment_amount = $request->prepayment_amount;
            $prepayment->save();

            $product->preorder_prepayment_id   = $prepayment->id;
            $product->save();
        }

        // Coupon
        if ($request->is_coupon) {
            $coupon_start_date = null;
            $coupon_end_date   = null;
            if ($request['coupon_date_range'] != null) {
                $date_var               = explode(" to ", $request['coupon_date_range']);
                $coupon_start_date = strtotime($date_var[0]);
                $coupon_end_date   = strtotime($date_var[1]);
            }

            $coupoon = $preorderProduct->preorder_coupon ?? new PreorderCoupon();
            $coupoon->preorder_product_id = $product->id;
            $coupoon->coupon_code = $request->coupon_code;
            $coupoon->coupon_amount = $request->coupon_amount;
            $coupoon->coupon_type = $request->coupon_type;
            $coupoon->coupon_benefits = json_encode($request->coupon_benefits);
            $coupoon->coupon_instructions = $request->coupon_instructions;
            $coupoon->coupon_start_date = $coupon_start_date;
            $coupoon->coupon_end_date = $coupon_end_date;
            $coupoon->save();

            $product->preorder_coupon_id   = $coupoon->id;
            $product->save();
        }

        // Discount
        if ($request->is_Advance_discount) {
            $discount = $preorderProduct->preorder_discount ?? new PreorderDiscount();
            $discount->preorder_product_id = $product->id;
            $discount->after_preorder_discount_type = $request->after_preorder_discount_type;
            $discount->after_preorder_discount_amount = $request->after_preorder_discount_amount;
            $discount->direct_purchase_discount_type = $request->direct_purchase_discount_type;
            $discount->direct_purchase_discount_amount = $request->direct_purchase_discount_amount;
            $discount->save();

            $product->preorder_discount_id   = $discount->id;
            $product->save();
        }

        // Refund
        if ($request->is_refundable) {
            $refund = $preorderProduct->preorder_refund ?? new PreorderRefund();
            $refund->preorder_product_id = $product->id;
            $refund->show_refund_note = $request->show_refund_note;
            $refund->note_id = $request->refund_note_id;
            $refund->save();

            $product->preorder_refund_id   = $refund->id;
            $product->save();
        }

        // Shipping
        $shipping = $preorderProduct->preorder_shipping ?? new PreorderShipping();
        $shipping->preorder_product_id = $product->id;
        $shipping->shipping_type = $request->shipping_type;
        $shipping->shipping_time = $request->est_shipping_days;
        $shipping->show_shipping_time = $request->show_shipping_time;
        $shipping->min_shipping_days = $request->min_shipping_days;
        $shipping->max_shipping_days = $request->max_shipping_days;
        $shipping->show_shipping_note = $request->show_shipping_note;
        $shipping->note_id = $request->shipping_note_id;
        $shipping->save();

        $product->preorder_shipping_id   = $shipping->id;
        $product->save();

        // cash on deliver (COD)
        if ($request->is_cod) {
            $cod = $product->preorder_cod ?? new PreorderCashondelivery();
            $cod->preorder_product_id = $product->id;
            $cod->prepayment_needed = $request->prepayment_needed;
            $cod->show_cod_note = $request->show_cod_note;
            $cod->note_id = $request->delivery_note_id;
            $cod->save();

            $product->preorder_cashondelivery_id   = $cod->id;
            $product->save();
        }

        // stcok
        if ($request->is_stock_visibility) {

            $stock = $preorderProduct->preorder_stock ?? new PreorderStock();
            $stock->preorder_product_id = $product->id;
            $stock->stock_visibility_state = $request->stock_visibility_state;
            $stock->current_stock = $request->current_stock;
            $stock->low_stock_stock = $request->low_stock_stock;
            $stock->is_low_stock_warning = $request->is_low_stock_warning;
            $stock->is_custom_order_show = $request->is_custom_order_show;
            $stock->preorder_quantity = $request->preorder_quantity;
            $stock->final_order_quantity = $request->final_order_quantity;
            $stock->save();

            $product->preorder_stock_id   = $stock->id;
            $product->save();
        }

        // Product Translations
        $product_translation = PreorderProductTranslation::firstOrNew(['lang' => $request->lang, 'preorder_product_id' => $product->id]);
        $product_translation->product_name = $request->product_name;
        $product_translation->unit         = $request->unit;
        $product_translation->description  = $request->description;
        $product_translation->save();
    }

    // Preorder Product Destroy
    public function productdestroy($id)
    {
        $preorderProduct = PreorderProduct::findOrFail($id);

        $preorderProduct->preorder_product_taxes()->delete();
        $preorderProduct->preorder_prepayment()->delete();
        $preorderProduct->preorder_sample_order()->delete();
        $preorderProduct->preorder_wholesale_prices()->delete();
        $preorderProduct->preorder_coupon()->delete();
        $preorderProduct->preorder_discount()->delete();
        $preorderProduct->preorder_discount_periods()->delete();
        $preorderProduct->preorder_refund()->delete();
        $preorderProduct->preorder_shipping()->delete();
        $preorderProduct->preorder_cod()->delete();
        $preorderProduct->preorder_stock()->delete();
        $preorderProduct->preorder_product_translations()->delete();
        $preorderProduct->preorderProductQueries()->delete();
        foreach($preorderProduct->preorderConversations() as $conversationThreads){
            $conversationThreads->messages()->delete();
        }
        
        $preorderProduct->delete();
    }

    // Preorder Status Update
    public function preorderStatusUpdate(Request $request, $id)
    {
        // dd($request->all());
        $statusType = '';
        $preorder = Preorder::find($id);
        if($request->preorder_request_status){
            $preorder->request_preorder_status = $request->status;
            $preorder->request_preorder_time = now();
            $preorder->status = 'request_preorder_status';
            $preorder->save();
            $statusType = $request->status == 2 ? 'request_accepted' : 'request_denied';
        }
        elseif($request->prepayment_confirm_status){
            if($preorder->request_preorder_status !== 2){
                flash(translate('You have to complete the previous step at first'))->warning();
                return back();
            }
            $preorder->prepayment_confirm_status = $request->status;
            $preorder->prepayment_note = $request->prepayment_note;
            $preorder->prepayment_confirmation_time = now();
            $preorder->status = 'prepayment_confirm_status';
            $preorder->save();
            $statusType = $request->status == 2 ? 'prepayment_request_accepted' : 'prepayment_request_denied';
        }
        elseif($request->final_order_status){
            if($preorder->prepayment_confirm_status !== 2){
                flash(translate('You have to complete the previous step at first'))->warning();
                return back();
            }
            $preorder->final_order_status = $request->status;
            $preorder->final_oder_note = $request->final_oder_note;
            $preorder->final_order_time = now();
            $preorder->status = 'final_order_status';
            $preorder->save();
            $statusType = $request->status == 2 ? 'final_request_accepted' : 'final_request_denied';
        }
        elseif($request->shipping_status){
            if($preorder->final_order_status !== 2){
                flash(translate('You have to complete the previous step at first'))->warning();
                return back();
            }
            $preorder->shipping_status = $request->status;
            $preorder->shipping_time = now();
            $preorder->shipping_proof = $request->shipping_proof;
            $preorder->shipping_note = $request->shipping_note;
            $preorder->status = 'shipping_status';
            $preorder->save();
            $statusType = $request->status == 2 ? 'product_in_shipping' : 'product_shipping_cancelled';
        }
        elseif($request->delivery_status){
            if($preorder->shipping_status !== 2){
                flash(translate('You have to complete the previous step at first'))->warning();
                return back();
            }
            $preorder->delivery_status = $request->status;
            $preorder->delivery_note = $request->delivery_note;
            $preorder->delivery_time = now();
            $preorder->status = 'delivery_status';
            $preorder->save();
            $statusType = $request->status == 2 ? 'product_delivered' : 'product_delivery_cancelled';

            // Seller Commission Calculate
            if($request->status == 2 && ($preorder->product_owner == 'seller')){
                $sellerCommission = get_setting('preorder_seller_commission');
                if($sellerCommission > 0){

                    $finalPrice = $preorder->subtotal - $preorder->coupon_discount; 
                    $admin_commission = ($finalPrice * $sellerCommission) / 100;
                    $seller_earning = $preorder->grand_total - $admin_commission;

                    // Admin to pay seller
                    $seller = $preorder->shop;
                    $seller->admin_to_pay -= $admin_commission;
                    $seller->save();

                    // Seller Commission History
                    $commission_history = new PreorderCommissionHistory;
                    $commission_history->preorder_id = $preorder->id;
                    $commission_history->seller_id = $preorder->product_owner_id;
                    $commission_history->admin_commission = $admin_commission;
                    $commission_history->seller_earning = $seller_earning;
                    $commission_history->save();
                }
            }
        }
        elseif($request->refund_status){
            if($preorder->delivery_status !== 2){
                flash(translate('You have to complete the previous step at first'))->warning();
                return back();
            }
            $preorder->refund_status = $request->status;
            $preorder->seller_refund_note = $request->seller_refund_note;
            $preorder->refund_time = now();
            $preorder->status = 'refund_status';
            $preorder->save();
            $statusType = $request->status == 2 ? 'product_refund_accepted' : 'product_refund_denied';
        }
        
        elseif($request->preorder_cancel_request){
            $current_status = $preorder->status;
            $preorder->update([
                $current_status => 3,
            ]);
        }

        //Send web Notifications to user, product Owner, if product Owner is not admin, admin too
        PreorderNotificationUtility::preorderNotification($preorder, $statusType);
    }
}
