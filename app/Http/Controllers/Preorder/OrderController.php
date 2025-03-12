<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Carrier;
use App\Models\Cart;
use App\Models\Preorder;
use Auth;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\Language;
use App\Notifications\PreorderNotification;
use App\Services\PreorderService;
use Carbon\Carbon;
use Session;
use PDF;
use Config;
use Notification;
use Route;

class OrderController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_preorders|view_all_inhouse_preorders|view_all_seller_preorders|view_all_delayed_prepayment_preorders|view_all_final_preorders'])->only('order_list');
        $this->middleware(['permission:view_preorder_details'])->only('show');
        $this->middleware(['permission:delete_preorder'])->only('destroy', 'bulkPreorderDelete');
    }

    public function order_list(Request $request)
    {
        $orders         = Preorder::latest();
        $routeName      = Route::currentRouteName();
        $adminId        = get_admin()->id;
        $data['date']   = $request->date;
        $data['sort_search'] = $request->search ?? null;
        $status = $request->order_status ?? 'all';
        
        $canSendNotification =  false;

        // All Preorders
        if ($routeName == 'all_preorder.list') {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->whereProductOwnerId($adminId);
            }
        }

        // Inhouse Preorders
        elseif ($routeName == 'inhouse_preorder.list') {
            $orders = $orders->whereProductOwnerId($adminId);
        }
        
        // Sellers Preorders
        elseif ($routeName == 'seller_preorder.list') {
            $orders = $orders->where('product_owner_id', '!=', $adminId);
        }

        // Not completed Prepayment Preorders
        elseif($routeName == 'delayed_prepayment_preorders.list'){
            $orders->where('prepayment_confirm_status', 0)
                    ->where('request_preorder_status', 2)
                    ->where('final_order_status', 0)
                    ->whereHas('preorder_product', function ($query) {
                        $query->where('is_prepayment', 1);
                    })
                ->where('request_preorder_time', '<', Carbon::now()->subDay());
            $canSendNotification =  get_notification_type('preorder_prepayment_reminder_customer', 'type')->status == 1 ? true : false;
        }

        // Not completed Final Preorders
        elseif($routeName == 'delayed_final_orders.list'){

            $orders->where(function ($query) {
                $query->whereHas('preorder_product', function ($subQuery) {
                    // Check if prepayment is enabled, check from Prepayment confirm status & Time
                    $subQuery->where('is_prepayment', 1);
                })
                ->where('prepayment_confirm_status', 2)
                ->where('final_order_status', 0)
                ->where('prepayment_confirmation_time', '<', Carbon::now()->subDay())
                ->orWhere(function ($subQuery) {
                    // When prepayment is disabled, check from Final Order confirm status & Time
                    $subQuery->whereHas('preorder_product', function ($innerQuery) {
                        $innerQuery->where('is_prepayment', 0);
                    })
                    ->where('request_preorder_status', 2)
                    ->where('final_order_status', 0)
                    ->where('request_preorder_time', '<', Carbon::now()->subDay());
                });
            });

            $canSendNotification =  get_notification_type('preorder_final_order_reminder_customer', 'type')?->status == 1 ? true : false;
        }
        
        // Filter by Status 
        if($status != null){

            if($status == 'requested'){
                $orders->where('request_preorder_status', 1);
            }
            elseif($status == 'accepted_requests'){
                $orders->where('request_preorder_status', 2)->where('prepayment_confirm_status',0);
            }
            elseif($status == 'prepayment_requests'){
                $orders->where('prepayment_confirm_status',1);
            }
            elseif($status == 'confirmed_prepayments'){
                $orders->where('prepayment_confirm_status', 2)->where('final_order_status', 0);
            }
            elseif($status == 'final_preorders'){
                $orders->where('final_order_status', [1,2]);
            }
            elseif($status == 'in_shipping'){
                $orders->where('shipping_status',2)->where('delivery_status', 0);
            }
            elseif($status == 'delivered'){
                $orders->where('delivery_status', 2);
            }
            elseif($status == 'refund'){
                $orders->where('refund_status',2);
            }
        }
        $data['status'] = $status;
        
        // Filter By date
        if ($data['date'] != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $data['date'])[0])) . '  00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $data['date'])[1])) . '  23:59:59');
        }

        // Search by order Code
        if ($data['sort_search']) {
            $orders = $orders->where('order_code', 'like', '%' .  $data['sort_search'] . '%');
        }
        
        $data['orders'] = $orders->paginate(15);
        $data['canSendNotification'] = $canSendNotification;

        
        $preorder_count = Preorder::query();
        $preorder_request_count = Preorder::where('request_preorder_status',1);
        $accepted_request_count = Preorder::where('request_preorder_status',2)->where('prepayment_confirm_status', 0);
        $prepayment_request_count = Preorder::where('prepayment_confirm_status',1);
        $confirmed_prepayment_request_count = Preorder::where('prepayment_confirm_status',2)->where('final_order_status', 0);
        $final_preorder_request_count = Preorder::where('final_order_status', [1,2]);
        $preorder_request_in_shipping_count = Preorder::where('shipping_status',2)->where('delivery_status', 0);
        $preorder_product_delivered_count = Preorder::where('delivery_status',2);
        $preorder_product_refunded_count = Preorder::where('refund_status',2);

        // All or Inhouse Preorders
        if (in_array($routeName, ['all_preorder.list', 'inhouse_preorder.list'])) {
            $adminCheck = true;
            if ($routeName == 'all_preorder.list' && get_setting('vendor_system_activation') == 1) {
                $adminCheck = false;
            }
            if($adminCheck){
                $preorder_count->whereProductOwnerId($adminId);
                $preorder_request_count->whereProductOwnerId($adminId);
                $accepted_request_count->whereProductOwnerId($adminId);
                $prepayment_request_count->whereProductOwnerId($adminId);
                $confirmed_prepayment_request_count->whereProductOwnerId($adminId);
                $final_preorder_request_count->whereProductOwnerId($adminId);
                $preorder_request_in_shipping_count->whereProductOwnerId($adminId);
                $preorder_product_delivered_count->whereProductOwnerId($adminId);
                $preorder_product_refunded_count->whereProductOwnerId($adminId);
            }
        }

        // Sellers Preorders
        elseif ($routeName == 'seller_preorder.list') {
            $preorder_count->where('product_owner_id', '!=', $adminId);
            $preorder_request_count->where('product_owner_id', '!=', $adminId);
            $accepted_request_count->where('product_owner_id', '!=', $adminId);
            $prepayment_request_count->where('product_owner_id', '!=', $adminId);
            $confirmed_prepayment_request_count->where('product_owner_id', '!=', $adminId);
            $final_preorder_request_count->where('product_owner_id', '!=', $adminId);
            $preorder_request_in_shipping_count->where('product_owner_id', '!=', $adminId);
            $preorder_product_delivered_count->where('product_owner_id', '!=', $adminId);
            $preorder_product_refunded_count->where('product_owner_id', '!=', $adminId);
        }

        $data['preorder_count'] = $preorder_count->count();
        $data['preorder_request_count'] = $preorder_request_count->count();
        $data['accepted_request_count'] = $accepted_request_count->count();
        $data['prepayment_request_count'] = $prepayment_request_count->count();
        $data['confirmed_prepayment_request_count'] = $confirmed_prepayment_request_count->count();
        $data['final_preorder_request_count'] = $final_preorder_request_count->count();
        $data['preorder_request_in_shipping_count'] = $preorder_request_in_shipping_count->count();
        $data['preorder_product_delivered_count'] = $preorder_product_delivered_count->count();
        $data['preorder_product_refunded_count'] = $preorder_product_refunded_count->count();

        return view('preorder.backend.orders.index', $data);
    }


    public function show($id)
    {
        $order =  Preorder::with(['preorder_product','user','address'])->find(decrypt($id));
        $order->update(['is_viewed'=>true]);
        $sort_search = '';
        return view('preorder.backend.orders.show', compact('sort_search', 'order'));
    }
    
    public function customer_order_list()
    {
        $order = new Preorder();
        $sort_search = '';
        return view('preorder.frontend.order.index', compact('sort_search', 'order'));
    }

    public function customer_order(Request $request, $id)
    {
        $order = new Preorder();
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

        if (auth()->check()) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->active()->get();
            $addresses = Address::where('user_id', $user_id)->get();
            if (count($addresses)) {
                $address = $addresses->toQuery()->first();
                $address_id = $address->id;
                $country_id = $address->country_id;
                $city_id = $address->city_id;
                $default_address = $addresses->toQuery()->where('set_default', 1)->first();
                if ($default_address != null) {
                    $address_id = $default_address->id;
                    $country_id = $default_address->country_id;
                    $city_id = $default_address->city_id;
                }
            }
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->active()->get() : [];
        }

        $shipping_info['country_id'] = $country_id;
        $shipping_info['city_id'] = $city_id;
        $default_carrier_id = null;
        $default_shipping_type = 'home_delivery';

        $carrier_list = Carrier::where('status', 1)->get();
        $review_status = (auth()->check() && (Preorder::whereProductId($order->preorder_product->id)->where('user_id', auth()->user()->id)->whereDeliveryStatus(2)->count() > 0) ) ? 1 : 0;
        
        return view('preorder.frontend.order.show', compact('sort_search', 'order',  'address_id',  'carrier_list', 'shipping_info','review_status'));
    }

    public function order_status_update(Request $request, $id){
        (new PreorderService)->preorderStatusUpdate($request, $id);

        flash(translate('Data has been updated successfully'))->success();
        return redirect()->back();
    }


    public function bulkPreorderDelete(Request $request)
    {
        if ($request->order_ids) {
            foreach ($request->order_ids as $order_id) {
                Preorder::destroy($order_id);
            }
        }
        return 1;
    }

    public function destroy($id){
        Preorder::destroy($id);
        flash(translate('Data has been deleted successfully'))->success();
        return redirect()->back();
    }

    public function invoice_download($id){
        if (Session::has('currency_code')) {
            $currency_code = Session::get('currency_code');
        } else {
            $currency_code = Currency::findOrFail(get_setting('system_default_currency'))->code;
        }
        $language_code = Session::get('locale', Config::get('app.locale'));

        if (Language::where('code', $language_code)->first()->rtl == 1) {
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        } else {
            $direction = 'ltr';
            $text_align = 'left';
            $not_text_align = 'right';
        }

        if (
            $currency_code == 'BDT' ||
            $language_code == 'bd'
        ) {
            // bengali font
            $font_family = "'Hind Siliguri','freeserif'";
        } elseif (
            $currency_code == 'KHR' ||
            $language_code == 'kh'
        ) {
            // khmer font
            $font_family = "'Hanuman','sans-serif'";
        } elseif ($currency_code == 'AMD') {
            // Armenia font
            $font_family = "'arnamu','sans-serif'";
            // }elseif($currency_code == 'ILS'){
            //     // Israeli font
            //     $font_family = "'Varela Round','sans-serif'";
        } elseif (
            $currency_code == 'AED' ||
            $currency_code == 'EGP' ||
            $language_code == 'sa' ||
            $currency_code == 'IQD' ||
            $language_code == 'ir' ||
            $language_code == 'om' ||
            $currency_code == 'ROM' ||
            $currency_code == 'SDG' ||
            $currency_code == 'ILS' ||
            $language_code == 'jo'
        ) {
            // middle east/arabic/Israeli font
            $font_family = "xbriyaz";
        } elseif ($currency_code == 'THB') {
            // thai font
            $font_family = "'Kanit','sans-serif'";
        } elseif (
            $currency_code == 'CNY' ||
            $language_code == 'zh'
        ) {
            // Chinese font
            $font_family = "'sun-exta','gb'";
        } elseif (
            $currency_code == 'MMK' ||
            $language_code == 'mm'
        ) {
            // Myanmar font
            $font_family = 'tharlon';
        } elseif (
            $currency_code == 'THB' ||
            $language_code == 'th'
        ) {
            // Thai font
            $font_family = "'zawgyi-one','sans-serif'";
        } elseif (
            $currency_code == 'USD'
        ) {
            // Thai font
            $font_family = "'Roboto','sans-serif'";
        } else {
            // general for all
            $font_family = "freeserif";
        }

        // $config = ['instanceConfigurator' => function($mpdf) {
        //     $mpdf->showImageErrors = true;
        // }];
        // mpdf config will be used in 4th params of loadview

        $config = [];

        $order = Preorder::with(['preorder_product','user','address','shop'])->findOrFail($id);
        if (in_array(auth()->user()->user_type, ['admin','staff']) || in_array(auth()->id(), [$order->user_id, $order->product_owner_id])) {
            return PDF::loadView('preorder.backend.invoices.invoice', [
                'order' => $order,
                'font_family' => $font_family,
                'direction' => $direction,
                'text_align' => $text_align,
                'not_text_align' => $not_text_align
            ], [], $config)->download('order-' . $order->order_code . '.pdf');
        }
        flash(translate("You do not have the right permission to access this invoice."))->error();
        return redirect()->back();
    }
    public function invoice_preview($id){
        if (Session::has('currency_code')) {
            $currency_code = Session::get('currency_code');
        } else {
            $currency_code = Currency::findOrFail(get_setting('system_default_currency'))->code;
        }
        $language_code = Session::get('locale', Config::get('app.locale'));

        if (Language::where('code', $language_code)->first()->rtl == 1) {
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        } else {
            $direction = 'ltr';
            $text_align = 'left';
            $not_text_align = 'right';
        }

        if (
            $currency_code == 'BDT' ||
            $language_code == 'bd'
        ) {
            // bengali font
            $font_family = "'Hind Siliguri','freeserif'";
        } elseif (
            $currency_code == 'KHR' ||
            $language_code == 'kh'
        ) {
            // khmer font
            $font_family = "'Hanuman','sans-serif'";
        } elseif ($currency_code == 'AMD') {
            // Armenia font
            $font_family = "'arnamu','sans-serif'";
            // }elseif($currency_code == 'ILS'){
            //     // Israeli font
            //     $font_family = "'Varela Round','sans-serif'";
        } elseif (
            $currency_code == 'AED' ||
            $currency_code == 'EGP' ||
            $language_code == 'sa' ||
            $currency_code == 'IQD' ||
            $language_code == 'ir' ||
            $language_code == 'om' ||
            $currency_code == 'ROM' ||
            $currency_code == 'SDG' ||
            $currency_code == 'ILS' ||
            $language_code == 'jo'
        ) {
            // middle east/arabic/Israeli font
            $font_family = "xbriyaz";
        } elseif ($currency_code == 'THB') {
            // thai font
            $font_family = "'Kanit','sans-serif'";
        } elseif (
            $currency_code == 'CNY' ||
            $language_code == 'zh'
        ) {
            // Chinese font
            $font_family = "'sun-exta','gb'";
        } elseif (
            $currency_code == 'MMK' ||
            $language_code == 'mm'
        ) {
            // Myanmar font
            $font_family = 'tharlon';
        } elseif (
            $currency_code == 'THB' ||
            $language_code == 'th'
        ) {
            // Thai font
            $font_family = "'zawgyi-one','sans-serif'";
        } elseif (
            $currency_code == 'USD'
        ) {
            // Thai font
            $font_family = "'Roboto','sans-serif'";
        } else {
            // general for all
            $font_family = "freeserif";
        }

        $config = [];

        $order = Preorder::with(['preorder_product','user','address','shop'])->findOrFail($id);
        if (in_array(auth()->user()->user_type, ['admin','staff']) || in_array(auth()->id(), [$order->user_id, $order->product_owner_id])) {
            return PDF::loadView('preorder.backend.invoices.invoice', [
                'order' => $order,
                'font_family' => $font_family,
                'direction' => $direction,
                'text_align' => $text_align,
                'not_text_align' => $not_text_align
            ], [], $config)->stream('order-' . $order->order_code . '.pdf');
        }
        flash(translate("You do not have the right permission to access this invoice."))->error();
        return redirect()->back();
    }

    public function prepaymentFinalPreorderReminder(Request $request){
        if($request->order_ids != null){
            $notificationType = get_notification_type($request->reminder_type, 'type');
            if($notificationType->status == 1){
                foreach (explode(",",$request->order_ids) as $order_id) {
                    $preorder = Preorder::where('id', $order_id)->first();
                    $user = $preorder->user;
                    $order_notification['preorder_id'] = $preorder->id;
                    $order_notification['order_code'] = $preorder->order_code;
                    $order_notification['notification_type_id'] = $notificationType->id;
                    Notification::send($user, new PreorderNotification($order_notification));
                }
            }
            flash(translate('Notification Sent Successfully.'))->success();
        }
        else{
            flash(translate('Something went wrong!.'))->warning();
        }
        return back();
    }
}
