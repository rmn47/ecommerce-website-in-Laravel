<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Carrier;
use App\Models\Cart;
use App\Models\Country;
use App\Models\Preorder;
use App\Models\PreorderCoupon;
use App\Models\PreorderProduct;
use App\Utility\PreorderNotificationUtility;
use Illuminate\Http\Request;

class PreorderController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:preorder_settings'])->only('preorderSettings');
    }
    

    public function place_order(Request $request){

        $product = PreorderProduct::find($request->preorder_product_id);
        $tax = 0;
        $product_discount = 0;
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += (($product->unit_price * $product->min_qty) * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $tax *= $product->min_qty;
        $shipping_cost = 0;
        if($product->preorder_shipping->shipping_type != null && $product->preorder_shipping->shipping_type = 'flat'){
            $shipping_cost += get_setting('preorder_flat_rate_shipping');
        }

        $product_discount = $product->discount_type == 'flat' ?   $product->discount : ($product->discount * $product->unit_price) /100;

        $currentTimestamp = strtotime(date('d-m-Y'));

        if($product->discount_start_date == null && ($currentTimestamp < $product->discount_start_date || $currentTimestamp > $product->discount_end_date)){
            $product_discount = 0;
        }

        $product_price = $product->unit_price - $product_discount;

        $total_product_price = $product_price * $product->min_qty;

        $preorder = new Preorder();

        $preorder->product_id  = $request->preorder_product_id;
        $preorder->user_id  = auth()->id();
        $preorder->product_owner_id  = $product->user_id;
        $preorder->product_owner = $product->user->user_type;
        $preorder->subtotal  = $product_price * $product->min_qty;
        $preorder->grand_total  = $total_product_price + $tax + $shipping_cost;
        $preorder->tax  = $tax;
        $preorder->product_discount  = $product_discount * $product->min_qty;
        $preorder->shipping_cost  = $shipping_cost;
        $preorder->quantity  = $product->min_qty;
        $preorder->unit_price  = $product->unit_price;
        $preorder->order_code  = date('Ymd-His') . rand(10, 99);
        $preorder->request_note = $request->request_note;
        $preorder->request_preorder_status = 1;
        $preorder->request_preorder_time = now();
        $preorder->save();
        $statusType = 'request';

        flash(translate('Preorder request submitted successfully!!'))->success();
        return redirect()->route('preorder.order_details',encrypt($preorder->id));
    }

    public function order_list(){
        $orders = Preorder::where('user_id', auth()->id())->orderBy('created_at','desc')->paginate(15);
        return view('preorder.frontend.order.purchase_history', compact('orders'));
    }

    public function order_details (Request $request, $id){
        $order = Preorder::with('preorder_product')->find(decrypt($id));

        if(!$order){
            flash(translate('No order found!!'))->success();
            return redirect()->back();
        }
        
        $sort_search = '';

        if (get_setting('guest_checkout_activation') == 0 && auth()->user() == null) {
            return redirect()->route('user.login');
        }

        if (auth()->check() && !$request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        $country_id = 0;
        $city_id = 0;
        $address_id = 0;
        $shipping_info = array();
        $shipping_info['country_id'] = $country_id;
        $shipping_info['city_id'] = $city_id;

        $default_carrier_id = null;
        $default_shipping_type = 'home_delivery';

        $carrier_list = Carrier::where('status', 1)->get();
        $review_status =  (auth()->check() && (Preorder::whereProductId($order->preorder_product->id)->where('user_id', auth()->user()->id)->whereDeliveryStatus(2)->count() > 0) ) ? 1 : 0;

        return view('preorder.frontend.order.show', compact('sort_search', 'order',  'address_id',  'carrier_list', 'shipping_info','review_status'));
    }

    public function order_update(Request $request, $id){
        $order = Preorder::find($id);
        // dd($request->all());
        if(! $order){
            flash(translate('No order found!!'))->success();
            return redirect()->back();
        }

        if($request->request_preorder){
            $order->request_note = $request->request_note;
            $order->request_preorder_status = 1;
            $order->request_preorder_time = now();
            $order->status = 'request_preorder_status';
            $order->save();
            $statusType = 'request';
        }

        if($request->prepayment_confirmation){
            $order->prepayment  = $order->preorder_product?->preorder_prepayment?->prepayment_amount;
            $order->payment_proof = $request->payment_proof;
            $order->reference_no = $request->reference_no;
            $order->confirm_note = $request->confirm_note;
            $order->cod_for_prepayment = $request->cod_for_prepayment;
            $order->prepayment_confirm_status = 1;
            $order->prepayment_confirmation_time = now();
            $statusType = 'prepayment_request';
            $order->status = 'prepayment_confirm_status';
            $order->save();
        }

        if($request->final_order){
            $order->address_id = $request->address_id;
            $order->pickup_point_id = $request->pickup_point_id;
            $order->delivery_type = $request->shipping_type;
            $order->cod_for_final_order = $request->cod_for_final_order;
            $order->prepayment_confirmation_time = now();
            $order->final_order_status = 1;
            $order->final_payment_proof = $request->final_payment_proof;
            $order->final_payment_reference_no = $request->final_payment_reference_no;
            $order->final_payment_confirm_note = $request->final_payment_confirm_note;
            $statusType = 'final_request';
            $order->status = 'final_order_status';
            $order->save();
        }

        if($request->refund_request){
            $order->refund_status = 1;
            $order->refund_proof = $request->refund_proof;
            $order->refund_note = $request->refund_note;
            $statusType = 'product_refund_request';
            $order->status = 'refund_status';
            $order->save();
        }

        //Send web Notifications to user, product Owner, if product Owner is not admin, admin too
        PreorderNotificationUtility::preorderNotification($order, $statusType);

        flash(translate('Order updated!!'))->success();
        return redirect()->back();
    }

    // PreOrder Settings
    public function preorderSettings(){
        return view('preorder.backend.settings.index');
    }

    // PreOrder Settings
    public function updateDeliveryAddress(Request $request){
                $proceed = 0;
        $default_carrier_id = null;
        $default_shipping_type = 'home_delivery';
        $user = auth()->user();
        $shipping_info = array();

        $carts = $user != null ?
                Cart::where('user_id', $user->id)->active()->get() :
                Cart::where('temp_user_id', $request->session()->get('temp_user_id'))->active()->get();

        $carts->toQuery()->update(['address_id' => $request->address_id]);

        $country_id = $user != null ?
                    Address::findOrFail($request->address_id)->country_id :
                    $request->address_id;
        $city_id = $user != null ?
                    Address::findOrFail($request->address_id)->city_id :
                    $request->city_id;
        $shipping_info['country_id'] = $country_id;
        $shipping_info['city_id'] = $city_id;

        $carrier_list = array();
        if (get_setting('shipping_type') == 'carrier_wise_shipping') {
            $default_shipping_type = 'carrier';
            $zone = Country::where('id', $country_id)->first()->zone_id;

            $carrier_query = Carrier::where('status', 1);
            $carrier_query->whereIn('id',function ($query) use ($zone) {
                $query->select('carrier_id')->from('carrier_range_prices')
                    ->where('zone_id', $zone);
            })->orWhere('free_shipping', 1);
            $carrier_list = $carrier_query->get();

            if (count($carrier_list) > 1) {
                $default_carrier_id = $carrier_list->toQuery()->first()->id;
            }
        }

        $carts = $carts->fresh();

        foreach ($carts as $key => $cartItem) {
            if (get_setting('shipping_type') == 'carrier_wise_shipping') {
                $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info, $default_carrier_id);
            } else {
                $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info);
            }
            $cartItem['address_id'] = $user != null ? $request->address_id : 0;
            $cartItem['shipping_type'] = $default_shipping_type;
            $cartItem['carrier_id'] = $default_carrier_id;
            $cartItem->save();
        }

        $carts = $carts->fresh();

        return array(
            'delivery_info' => view('frontend.partials.cart.delivery_info', compact('carts', 'carrier_list', 'shipping_info'))->render(),
            'cart_summary' => view('frontend.partials.cart.cart_summary', compact('carts', 'proceed'))->render()
        );
    }

    public function apply_coupon_code(Request $request){
        $coupon = PreorderCoupon::where('preorder_product_id', $request->preorder_product_id)->where('coupon_code',$request->coupon_code)->first();


        // Coupon Code Invalid 
        if(!$coupon){
            flash(translate('Coupon is invalid!!'))->error();
            return redirect()->back();
        }

        if(!$coupon->preorder_product?->is_coupon){
            flash(translate('Coupon is not enabled for this product'))->warning();
            return redirect()->back();
        }

        $currentTimestamp = strtotime(date('d-m-Y'));
        
        if ($currentTimestamp < $coupon->coupon_start_date || $currentTimestamp > $coupon->coupon_end_date) {
            flash(translate('Coupon is invalid!!'))->error();
            return redirect()->back();
        }

        $order = Preorder::whereId($request->order_id)->first();
        $discount = $coupon->coupon_type == 'percent' ? ($order->subtotal * $coupon->coupon_amount)/100 : $coupon->coupon_amount;
        $order->is_coupon_applied = 1;
        $order->coupon_discount = $discount;
        $order->grand_total  -= $discount;
        $order->save();

        flash(translate('Coupon Applied!!'))->success();
        return redirect()->back();
    }

    public function remove_coupon_code(Request $request){
        $order = Preorder::whereId($request->order_id)->first();
        $order->is_coupon_applied = 0;
        $order->grand_total  += $order->coupon_discount;
        $order->coupon_discount = null;
        $order->save();

        flash(translate('Coupon Removed Successfully!!'))->success();
        return redirect()->back();
    }
}
