<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AffiliateController;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\CouponUsage;
use App\Models\Coupon;
use App\Models\User;
use App\Models\CombinedOrder;
use App\Models\SmsTemplate;
use Auth;
use Mail;
use App\Mail\InvoiceEmailManager;
use App\Models\OrdersExport;
use App\Utility\NotificationUtility;
use CoreComponentRepository;
use App\Utility\SendSMSUtility;
use App\Utility\EmailUtility;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_all_orders|view_inhouse_orders|view_seller_orders|view_pickup_point_orders|view_all_offline_payment_orders'])->only('all_orders');
        $this->middleware(['permission:view_order_details'])->only('show');
        $this->middleware(['permission:delete_order'])->only('destroy', 'bulk_order_delete');
    }

    public function all_orders(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $sort_search = null;
        $delivery_status = null;
        $payment_status = '';
        $order_type = '';

        $orders = Order::orderBy('id', 'desc');
        $admin_user_id = get_admin()->id;

        if (Route::currentRouteName() == 'inhouse_orders.index' && Auth::user()->can('view_inhouse_orders')) {
            $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
        } elseif (Route::currentRouteName() == 'seller_orders.index' && Auth::user()->can('view_seller_orders')) {
            $orders = $orders->where('orders.seller_id', '!=', $admin_user_id);
        } elseif (Route::currentRouteName() == 'pick_up_point.index' && Auth::user()->can('view_pickup_point_orders')) {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
            }
            $orders->where('shipping_type', 'pickup_point')->orderBy('code', 'desc');
            if (Auth::user()->user_type == 'staff' && Auth::user()->staff->pick_up_point != null) {
                $orders->where('shipping_type', 'pickup_point')
                    ->where('pickup_point_id', Auth::user()->staff->pick_up_point->id);
            }
        } elseif (Route::currentRouteName() == 'all_orders.index' && Auth::user()->can('view_all_orders')) {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
            }
        } elseif (Route::currentRouteName() == 'offline_payment_orders.index' && Auth::user()->can('view_all_offline_payment_orders')) {
            $orders = $orders->where('orders.manual_payment', 1);
            if ($request->order_type != null) {
                $order_type = $request->order_type;
                $orders = $order_type == 'inhouse_orders' ?
                    $orders->where('orders.seller_id', '=', $admin_user_id) :
                    $orders->where('orders.seller_id', '!=', $admin_user_id);
            }
        } elseif (Route::currentRouteName() == 'unpaid_orders.index' && Auth::user()->can('view_all_unpaid_orders')) {
            $orders = $orders->where('orders.payment_status', 'unpaid');
        } else {
            abort(403);
        }

        if ($request->search) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($date != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])) . ' 00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])) . ' 23:59:59');
        }
        $orders = $orders->paginate(15);
        $unpaid_order_payment_notification = get_notification_type('complete_unpaid_order_payment', 'type');
        return view('backend.sales.index', compact('orders', 'sort_search', 'order_type', 'payment_status', 'delivery_status', 'date', 'unpaid_order_payment_notification'));
    }

    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order_shipping_address = json_decode($order->shipping_address);
        $delivery_boys = User::where('city', $order_shipping_address->city)
            ->where('user_type', 'delivery_boy')
            ->get();

        if (env('DEMO_MODE') != 'On') {
            $order->viewed = 1;
            $order->save();
        }

        return view('backend.sales.show', compact('order', 'delivery_boys'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)->active()->get();
    
        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }
    
        $address = Address::where('id', $carts[0]['address_id'])->first();
    
        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name'] = Auth::user()->name;
            $shippingAddress['email'] = Auth::user()->email;
            $shippingAddress['address'] = $address->address;
            $shippingAddress['country'] = $address->country->name;
            $shippingAddress['state'] = $address->state->name;
            $shippingAddress['city'] = $address->city->name;
            $shippingAddress['postal_code'] = $address->postal_code;
            $shippingAddress['phone'] = $address->phone;
            if ($address->latitude || $address->longitude) {
                $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
            }
        }
    
        $combined_order = new CombinedOrder;
        $combined_order->user_id = Auth::user()->id;
        $combined_order->shipping_address = json_encode($shippingAddress);
        $combined_order->save();
    
        $seller_products = array();
        foreach ($carts as $cartItem) {
            $product_ids = array();
            $product = Product::find($cartItem['product_id']);
            if (isset($seller_products[$product->user_id])) {
                $product_ids = $seller_products[$product->user_id];
            }
            array_push($product_ids, $cartItem);
            $seller_products[$product->user_id] = $product_ids;
        }
    
        foreach ($seller_products as $seller_product) {
            $order = new Order;
            $order->combined_order_id = $combined_order->id;
            $order->user_id = Auth::user()->id;
            $order->shipping_address = $combined_order->shipping_address;
            $order->additional_info = $request->additional_info;
            $order->payment_type = $request->payment_option;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His') . rand(10, 99);
            $order->date = strtotime('now');
    
            // Set initial payment status based on payment type
            $order->payment_status = ($request->payment_option == 'cash_on_delivery') ? 'unpaid' : 'pending';
            $order->delivery_status = 'pending'; // Initial delivery status
    
            $order->save();
    
            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            $coupon_discount = 0;
    
            foreach ($seller_product as $cartItem) {
                $product = Product::find($cartItem['product_id']);
    
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $coupon_discount += $cartItem['discount'];
    
                $product_variation = $cartItem['variation'];
    
                $product_stock = $product->stocks->where('variant', $product_variation)->first();
                if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
                    flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                    $order->delete();
                    return redirect()->route('cart')->send();
                } elseif ($product->digital != 1) {
                    $product_stock->qty -= $cartItem['quantity'];
                    $product_stock->save();
                }
    
                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price = cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $order_detail->tax = cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $order_detail->shipping_type = $cartItem['shipping_type'];
                $order_detail->product_referral_code = $cartItem['product_referral_code'];
                $order_detail->shipping_cost = $cartItem['shipping_cost'];
    
                $shipping += $order_detail->shipping_cost;
    
                $order_detail->quantity = $cartItem['quantity'];
    
                if (addon_is_activated('club_point')) {
                    $order_detail->earn_point = $product->earn_point;
                }
    
                $order_detail->save();
    
                $product->num_of_sale += $cartItem['quantity'];
                $product->save();
    
                $order->seller_id = $product->user_id;
                $order->shipping_type = $cartItem['shipping_type'];
    
                if ($cartItem['shipping_type'] == 'pickup_point') {
                    $order->pickup_point_id = $cartItem['pickup_point'];
                }
                if ($cartItem['shipping_type'] == 'carrier') {
                    $order->carrier_id = $cartItem['carrier_id'];
                }
    
                if ($product->added_by == 'seller' && $product->user->seller != null) {
                    $seller = $product->user->seller;
                    $seller->num_of_sale += $cartItem['quantity'];
                    $seller->save();
                }
    
                if (addon_is_activated('affiliate_system')) {
                    if ($order_detail->product_referral_code) {
                        $referred_by_user = User::where('referral_code', $order_detail->product_referral_code)->first();
                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, $order_detail->quantity, 0, 0);
                    }
                }
            }
    
            $order->grand_total = $subtotal + $tax + $shipping;
    
            if ($seller_product[0]->coupon_code != null) {
                $order->coupon_discount = $coupon_discount;
                $order->grand_total -= $coupon_discount;
    
                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Coupon::where('code', $seller_product[0]->coupon_code)->first()->id;
                $coupon_usage->save();
            }
    
            $combined_order->grand_total += $order->grand_total;
    
            $order->save();
    
            // Send notifications based on payment type
            if ($request->payment_option == 'cash_on_delivery') {
                // Send Email 1: Database-driven email
                Log::info("Attempting to send database email for order confirmed: Order {$order->code}");
                EmailUtility::order_email($order, 'confirmed');
    
                // Send Email 2: Hardcoded email
                $this->sendHardcodedEmail($order, 'confirmed');
    
                // Send SMS notification
                try {
                    $serverUrl = env('MSGCLUB_SERVER_URL');
                    $authKey = env('MSGCLUB_AUTH_KEY');
                    $senderId = env('MSGCLUB_SENDER_ID');
                    $routeId = env('MSGCLUB_ROUTE_ID');
                    $smsContentType = env('MSGCLUB_LANGUAGE');
                    
                    $msg = "Dear {$order->user->name}, your order {$order->code} has been confirmed on " . env('APP_NAME') . ".";
    
                    $postData = [
                        'mobileNumbers' => json_decode($order->shipping_address)->phone,
                        'smsContent' => $msg,
                        'senderId' => $senderId,
                        'routeId' => $routeId,
                        "smsContentType" => $smsContentType
                    ];
                    $data_json = json_encode($postData);
                    $url = "http://" . $serverUrl . "/rest/services/sendSMS/sendGroupSms?AUTH_KEY=" . $authKey;
    
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Content-Length: ' . strlen($data_json)],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => $data_json,
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSL_VERIFYPEER => 0
                    ]);
    
                    $output = curl_exec($ch);
    
                    if (curl_errno($ch)) {
                        Log::error("Additional SMS sending failed for order confirmed: Order {$order->code} - " . curl_error($ch));
                    } else {
                        $obj = json_decode($output);
                        if ($obj->responseCode == 3001) {
                            Log::info("Additional SMS sent successfully to {$order->user->phone} for order confirmed: Order {$order->code}");
                        } else {
                            Log::error("Additional SMS API response error for order confirmed: Order {$order->code} - " . $output);
                        }
                    }
                    curl_close($ch);
                } catch (\Exception $smsException) {
                    Log::error("Failed to send additional SMS notification for order confirmed: Order {$order->code} - " . $smsException->getMessage());
                }
            } else {
                // For non-COD, send a "pending payment" notification
                Log::info("Attempting to send database email for order pending payment: Order {$order->code}");
                EmailUtility::order_email($order, 'pending');
    
                $this->sendHardcodedEmail($order, 'pending');
    
                // Send SMS notification for pending payment
                try {
                    $serverUrl = env('MSGCLUB_SERVER_URL');
                    $authKey = env('MSGCLUB_AUTH_KEY');
                    $senderId = env('MSGCLUB_SENDER_ID');
                    $routeId = env('MSGCLUB_ROUTE_ID');
                    $smsContentType = env('MSGCLUB_LANGUAGE');
                    
                    $msg = "Dear {$order->user->name}, your order {$order->code} is pending payment. Please complete the payment on " . env('APP_NAME') . ".";
    
                    $postData = [
                        'mobileNumbers' => json_decode($order->shipping_address)->phone,
                        'smsContent' => $msg,
                        'senderId' => $senderId,
                        'routeId' => $routeId,
                        "smsContentType" => $smsContentType
                    ];
                    $data_json = json_encode($postData);
                    $url = "http://" . $serverUrl . "/rest/services/sendSMS/sendGroupSms?AUTH_KEY=" . $authKey;
    
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Content-Length: ' . strlen($data_json)],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => $data_json,
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSL_VERIFYPEER => 0
                    ]);
    
                    $output = curl_exec($ch);
    
                    if (curl_errno($ch)) {
                        Log::error("Additional SMS sending failed for order pending payment: Order {$order->code} - " . curl_error($ch));
                    } else {
                        $obj = json_decode($output);
                        if ($obj->responseCode == 3001) {
                            Log::info("Additional SMS sent successfully to {$order->user->phone} for order pending payment: Order {$order->code}");
                        } else {
                            Log::error("Additional SMS API response error for order pending payment: Order {$order->code} - " . $output);
                        }
                    }
                    curl_close($ch);
                } catch (\Exception $smsException) {
                    Log::error("Failed to send additional SMS notification for order pending payment: Order {$order->code} - " . $smsException->getMessage());
                }
            }
        }
    
        $combined_order->save();
    
        $request->session()->put('combined_order_id', $combined_order->id);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            $order->commissionHistory()->delete();
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {
                    product_restock($orderDetail);
                } catch (\Exception $e) {
                }
                $orderDetail->delete();
            }

            // Send Email 1: Database-driven email
            Log::info("Attempting to send database email for order cancelled: Order {$order->code}");
            EmailUtility::order_email($order, 'cancelled');

            // Send Email 2: Hardcoded email
            $this->sendHardcodedEmail($order, 'cancelled');

            // Send SMS notification if available
            if (addon_is_activated('otp_system')) {
                $smsTemplate = SmsTemplate::where('identifier', 'order_cancelled_sms')->first();
                if ($smsTemplate && $smsTemplate->status == 1) {
                    try {
                        $phone = json_decode($order->shipping_address)->phone;
                        $text = "Your order {$order->code} has been cancelled.";
                        SendSMSUtility::sendSMS($phone, env('SMS_FROM_NUMBER', 'Store'), $text, null);
                        Log::info("SMS sent for order cancelled: Order {$order->code}");
                    } catch (\Exception $e) {
                        Log::error("SMS failed for order cancelled: Order {$order->code} - " . $e->getMessage());
                    }
                } else {
                    Log::warning("SMS template 'order_cancelled_sms' not found or inactive for Order {$order->code}");
                }
            }

            $order->delete();

            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function bulk_order_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $this->destroy($order_id);
            }
        }
        return 1;
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('seller.order_details_seller', compact('order'));
    }

    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();

        Log::info("Delivery status updated to '{$request->status}' for Order {$order->code}");

        if ($request->status == 'delivered') {
            $order->delivered_date = date("Y-m-d H:i:s");
            $order->save();

            // Send Email 1: Database-driven email
            Log::info("Attempting to send database email for order delivered: Order {$order->code}");
            EmailUtility::order_email($order, 'delivered');

            // Send Email 2: Hardcoded email
            $this->sendHardcodedEmail($order, 'delivered');

            if (addon_is_activated('otp_system')) {
                $smsTemplate = SmsTemplate::where('identifier', 'order_delivered_sms')->first();
                if ($smsTemplate && $smsTemplate->status == 1) {
                    try {
                        $phone = json_decode($order->shipping_address)->phone;
                        $text = "Your order {$order->code} has been delivered.";
                        SendSMSUtility::sendSMS($phone, env('SMS_FROM_NUMBER', 'Store'), $text, null);
                        Log::info("SMS sent for order delivered: Order {$order->code}");
                    } catch (\Exception $e) {
                        Log::error("SMS failed for order delivered: Order {$order->code} - " . $e->getMessage());
                    }
                } else {
                    Log::warning("SMS template 'order_delivered_sms' not found or inactive for Order {$order->code}");
                }
            }
        } elseif ($request->status == 'on_the_way') {
            // Send Email 1: Database-driven email
            Log::info("Attempting to send database email for order on_the_way: Order {$order->code}");
            EmailUtility::order_email($order, 'on_the_way');

            // Send Email 2: Hardcoded email
            $this->sendHardcodedEmail($order, 'on_the_way');

            if (addon_is_activated('otp_system')) {
                $smsTemplate = SmsTemplate::where('identifier', 'order_on_the_way_sms')->first();
                if ($smsTemplate && $smsTemplate->status == 1) {
                    try {
                        $phone = json_decode($order->shipping_address)->phone;
                        $text = "Your order {$order->code} is on the way. Tracking: {$order->tracking_code}";
                        SendSMSUtility::sendSMS($phone, env('SMS_FROM_NUMBER', 'Store'), $text, null);
                        Log::info("SMS sent for order on_the_way: Order {$order->code}");
                    } catch (\Exception $e) {
                        Log::error("SMS failed for order on_the_way: Order {$order->code} - " . $e->getMessage());
                    }
                } else {
                    Log::warning("SMS template 'order_on_the_way_sms' not found or inactive for Order {$order->code}");
                }
            }
        } elseif ($request->status == 'ready_for_pickup'|| $request->status == 'picked_up') {
            // Send Email 1: Database-driven email
            Log::info("Attempting to send database email for order ready_for_pickup: Order {$order->code}");
            EmailUtility::order_email($order, 'ready_for_pickup');

            // Send Email 2: Hardcoded email
            $this->sendHardcodedEmail($order, 'ready_for_pickup');

            if (addon_is_activated('otp_system')) {
                $smsTemplate = SmsTemplate::where('identifier', 'product_pickup_sms')->first();
                if ($smsTemplate && $smsTemplate->status == 1) {
                    try {
                        $phone = json_decode($order->shipping_address)->phone;
                        $text = "Your order {$order->code} is ready for pickup.";
                        SendSMSUtility::sendSMS($phone, env('SMS_FROM_NUMBER', 'Store'), $text, null);
                        Log::info("SMS sent for order ready_for_pickup: Order {$order->code}");
                    } catch (\Exception $e) {
                        Log::error("SMS failed for order ready_for_pickup: Order {$order->code} - " . $e->getMessage());
                    }
                } else {
                    Log::warning("SMS template 'product_pickup_sms' not found or inactive for Order {$order->code}");
                }
            }
        } elseif ($request->status == 'confirmed') {
            // Send Email 1: Database-driven email
            Log::info("Attempting to send database email for order confirmed: Order {$order->code}");
            EmailUtility::order_email($order, 'confirmed');

            // Send Email 2: Hardcoded email
            $this->sendHardcodedEmail($order, 'confirmed');
        } elseif ($request->status == 'cancelled') {
            // Send Email 1: Database-driven email
            Log::info("Attempting to send database email for order cancelled: Order {$order->code}");
            EmailUtility::order_email($order, 'cancelled');

            // Send Email 2: Hardcoded email
            $this->sendHardcodedEmail($order, 'cancelled');
        }

        if ($request->status == 'cancelled' && $order->payment_type == 'wallet') {
            $user = User::where('id', $order->user_id)->first();
            $user->balance += $order->grand_total;
            $user->save();
        }

        if ($request->status == 'cancelled' && $order->user->user_type == 'seller' && $order->payment_status == 'paid' && $order->commission_calculated == 1) {
            $sellerEarning = $order->commissionHistory->seller_earning;
            $shop = $order->shop;
            $shop->admin_to_pay -= $sellerEarning;
            $shop->save();
        }

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    product_restock($orderDetail);
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    product_restock($orderDetail);
                }

                if (addon_is_activated('affiliate_system')) {
                    if (($request->status == 'delivered' || $request->status == 'cancelled') && $orderDetail->product_referral_code) {
                        $no_of_delivered = 0;
                        $no_of_canceled = 0;

                        if ($request->status == 'delivered') {
                            $no_of_delivered = $orderDetail->quantity;
                        }
                        if ($request->status == 'cancelled') {
                            $no_of_canceled = $orderDetail->quantity;
                        }

                        $referred_by_user = User::where('referral_code', $orderDetail->product_referral_code)->first();
                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, 0, $no_of_delivered, $no_of_canceled);
                    }
                }
            }
        }

        NotificationUtility::sendNotification($order, $request->status);

        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated!";
            $status = str_replace("_", "", $order->delivery_status);
            $request->text = "Your order {$order->code} has been {$status}";
            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;
            NotificationUtility::sendFirebaseNotification($request);
        }

        if (addon_is_activated('delivery_boy') && Auth::user()->user_type == 'delivery_boy') {
            $deliveryBoyController = new DeliveryBoyController;
            $deliveryBoyController->store_delivery_history($order);
        }

        return 1;
    }

    public function update_tracking_code(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->tracking_code = $request->tracking_code;
        $order->save();

        return 1;
    }

    public function update_payment_status(Request $request)
    {
        // Log the incoming request for debugging
        Log::info("update_payment_status called with status: {$request->status}, order_id: {$request->order_id}");
    
        // Find the order
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();
    
        // Update payment status for order details based on user type
        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }
    
        // Determine overall payment status
        $status = 'paid';
        foreach ($order->orderDetails as $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
                break; // No need to continue if one is unpaid
            }
        }
        $order->payment_status = $status;
        $order->save();
    
        // Log the status update
        Log::info("Payment status updated to '{$status}' for Order {$order->code}");
    
        // Commission calculation for "paid" status
        // if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
        //     try {
        //         Log::info("Attempting commission calculation for Order {$order->code}");
        //         calculateCommissionAffilationClubPoint($order);
        //         Log::info("Commission calculated successfully for Order {$order->code}");
        //     } catch (\Exception $e) {
        //         Log::error("Commission calculation failed for Order {$order->code} - " . $e->getMessage());
        //     }
        // }
    
        // Email settings
        $fromAddress = env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_FROM_NAME');
        $subject = "Your App - Payment Status Update: {$status}";
        $data = [
            'order' => $order,
            'subject' => $subject,
            'header' => 'Payment Status Update',
            'message' => "Your order {$order->code} payment status has been updated to {$status}.",
            'status' => $status,
        ];
    
        // Send Email
        try {
            Log::info("Attempting to send email for payment status '{$status}' to {$order->user->email} for Order {$order->code}");
            Mail::send('emails.order_notification', $data, function ($message) use ($order, $subject, $fromAddress, $fromName) {
                $message->to($order->user->email)
                        ->from($fromAddress, $fromName)
                        ->subject($subject);
            });
            Log::info("Email sent successfully for payment status '{$status}' to {$order->user->email} for Order {$order->code}");
        } catch (\Exception $e) {
            Log::error("Failed to send email for payment status '{$status}' to {$order->user->email} for Order {$order->code} - " . $e->getMessage());
        }
    
        // Send SMS using msg.msgclub.net with environment variables
        try {
            $serverUrl = env('MSGCLUB_SERVER_URL');
            $authKey = env('MSGCLUB_AUTH_KEY');
            $senderId = env('MSGCLUB_SENDER_ID');
            $routeId = env('MSGCLUB_ROUTE_ID');
            $smsContentType = env('MSGCLUB_LANGUAGE');
            
            $smsMessage = "Dear {$order->user->name}, your order {$order->code} has been " . $status . " on " . env('APP_NAME') . ".";
    
            $postData = [
                'mobileNumbers' => json_decode($order->shipping_address)->phone,
                'smsContent' => $smsMessage,
                'senderId' => $senderId,
                'routeId' => $routeId,
                "smsContentType" => $smsContentType
            ];
            $data_json = json_encode($postData);
            $url = "http://{$serverUrl}/rest/services/sendSMS/sendGroupSms?AUTH_KEY={$authKey}";
    
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Content-Length: ' . strlen($data_json)],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data_json,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ]);
    
            $output = curl_exec($ch);
    
            if (curl_errno($ch)) {
                Log::error("SMS sending failed for payment status '{$status}' for Order {$order->code} - " . curl_error($ch));
            } else {
                $obj = json_decode($output);
                if ($obj && $obj->responseCode == 3001) {
                    Log::info("SMS sent successfully for payment status '{$status}' to {$order->user->phone} for Order {$order->code}");
                } else {
                    Log::error("SMS API response error for payment status '{$status}' for Order {$order->code} - Response: " . $output);
                }
            }
            curl_close($ch);
        } catch (\Exception $smsException) {
            Log::error("Failed to send SMS for payment status '{$status}' for Order {$order->code} - " . $smsException->getMessage());
        }
    
        return 1; // Success response
    }

    public function assign_delivery_boy(Request $request)
    {
        if (addon_is_activated('delivery_boy')) {
            $order = Order::findOrFail($request->order_id);
            $order->assign_delivery_boy = $request->delivery_boy;
            $order->delivery_history_date = date("Y-m-d H:i:s");
            $order->save();

            $delivery_history = \App\Models\DeliveryHistory::where('order_id', $order->id)
                ->where('delivery_status', $order->delivery_status)
                ->first();

            if (empty($delivery_history)) {
                $delivery_history = new \App\Models\DeliveryHistory;
                $delivery_history->order_id = $order->id;
                $delivery_history->delivery_status = $order->delivery_status;
                $delivery_history->payment_type = $order->payment_type;
            }
            $delivery_history->delivery_boy_id = $request->delivery_boy;
            $delivery_history->save();

            if (env('MAIL_USERNAME') != null && get_setting('delivery_boy_mail_notification') == '1') {
                $array['view'] = 'emails.invoice';
                $array['subject'] = translate('You are assigned to deliver an order. Order code') . ' - ' . $order->code;
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['order'] = $order;

                try {
                    Mail::to($order->delivery_boy->email)->queue(new InvoiceEmailManager($array));
                    Log::info("Delivery boy email queued for Order {$order->code}");
                } catch (\Exception $e) {
                    Log::error("Delivery boy email failed for Order {$order->code}: " . $e->getMessage());
                }
            }

            if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'assign_delivery_boy')->first()->status == 1) {
                try {
                    SendSMSUtility::assign_delivery_boy($order->delivery_boy->phone, $order->code);
                    Log::info("SMS sent for delivery boy assignment: Order {$order->code}");
                } catch (\Exception $e) {
                    Log::error("SMS failed for delivery boy assignment: Order {$order->code} - " . $e->getMessage());
                }
            }
        }

        return 1;
    }

    public function orderBulkExport(Request $request)
    {
        if ($request->id) {
            return Excel::download(new OrdersExport($request->id), 'orders.xlsx');
        }
        return back();
    }

    public function unpaid_order_payment_notification_send(Request $request)
    {
        if ($request->order_ids != null) {
            $notificationType = get_notification_type('complete_unpaid_order_payment', 'type');
            foreach (explode(",", $request->order_ids) as $order_id) {
                $order = Order::where('id', $order_id)->first();
                $user = $order->user;
                if ($notificationType->status == 1 && $order->payment_status == 'unpaid') {
                    $order_notification['order_id'] = $order->id;
                    $order_notification['order_code'] = $order->code;
                    $order_notification['user_id'] = $order->user_id;
                    $order_notification['seller_id'] = $order->seller_id;
                    $order_notification['status'] = $order->payment_status;
                    $order_notification['notification_type_id'] = $notificationType->id;
                    Notification::send($user, new OrderNotification($order_notification));
                }
            }
            flash(translate('Notification Sent Successfully.'))->success();
        } else {
            flash(translate('Something went wrong!.'))->warning();
        }
        return back();
    }

    /**
     * Send hardcoded email for various order statuses
     */
    protected function sendHardcodedEmail($order, $status)
    {
        switch ($status) {
            case 'confirmed':
                $subject = env('APP_NAME') . " - Order Confirmed";
                $header = translate('Great news!');
                $message = translate('Your order has been confirmed. Were preparing it for you.');
                break;
            case 'cancelled':
                $subject = env('APP_NAME') . " - Order Cancellation";
                $header = translate('We are sorry to inform you');
                $message = translate('Your order has been cancelled. We apologize for any inconvenience.');
                break;
            case 'delivered':
                $subject = env('APP_NAME') . " - Order Delivered";
                $header = translate('Good news!');
                $message = translate('Your order has been successfully delivered.');
                break;
            case 'on_the_way':
                $subject = env('APP_NAME') . " - Order On The Way";
                $header = translate('Your order is on its way!');
                $message = translate('Your order is currently being shipped to you.');
                break;
            case 'ready_for_pickup':
                $subject = env('APP_NAME') . " - Order Ready for Pickup";
                $header = translate('Ready for you!');
                $message = translate('Your order is ready for pickup at the designated location.');
                break;
            case 'paid':
                $subject = env('APP_NAME') . " - Payment Confirmed";
                $header = translate('Thank you!');
                $message = translate('Payment for your order has been successfully confirmed.');
                break;
            case 'unpaid':
                $subject = env('APP_NAME') . " - Payment Pending";
                $header = translate('Action required');
                $message = translate('Your order is still unpaid. Please complete the payment soon.');
                break;
            default:
                $subject = env('APP_NAME') . " - Order Update";
                $header = translate('Order update');
                $message = translate('Your order status has been updated.');
                break;
        }
    
        $data = [
            'order' => $order,
            'subject' => $subject,
            'header' => $header,
            'message' => $message,
            'status' => $status,
        ];
        
        $fromAddress = env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_FROM_NAME');
    
        // Send Email
        try {
            Mail::send('emails.order_notification', $data, function ($message) use ($order, $subject, $fromAddress, $fromName) {
                $message->to($order->user->email)
                        ->from($fromAddress, $fromName)
                        ->subject($subject);
            });
            Log::info("Hardcoded email sent successfully to {$order->user->email} for status: {$status}");
        } catch (\Exception $e) {
            Log::error("Failed to send hardcoded email for status {$status}: " . $e->getMessage());
        }
    
        // Send SMS
        try {
            $serverUrl = env('MSGCLUB_SERVER_URL');
            $authKey = env('MSGCLUB_AUTH_KEY');
            $senderId = env('MSGCLUB_SENDER_ID');
            $routeId = env('MSGCLUB_ROUTE_ID');
            $smsContentType = env('MSGCLUB_LANGUAGE');
            
            $smsMessage = "Dear {$order->user->name}, your order {$order->code} has been " . $status . " on " . env('APP_NAME') . ".";
    
            $postData = [
                'mobileNumbers' => json_decode($order->shipping_address)->phone,
                'smsContent' => $smsMessage,
                'senderId' => $senderId,
                'routeId' => $routeId,
                "smsContentType" => $smsContentType
            ];
            $data_json = json_encode($postData);
            $url = "http://" . $serverUrl . "/rest/services/sendSMS/sendGroupSms?AUTH_KEY=" . $authKey;
    
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Content-Length: ' . strlen($data_json)],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data_json,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ]);
    
            $output = curl_exec($ch);
    
            if (curl_errno($ch)) {
                Log::error("Additional SMS sending failed for order {$status}: Order {$order->code} - " . curl_error($ch));
            } else {
                $obj = json_decode($output);
                if ($obj->responseCode == 3001) {
                    Log::info("Additional SMS sent successfully to {$order->user->phone} for order {$status}: Order {$order->code}");
                } else {
                    Log::error("Additional SMS API response error for order {$status}: Order {$order->code} - " . $output);
                }
            }
            curl_close($ch);
        } catch (\Exception $smsException) {
            Log::error("Failed to send additional SMS notification for order {$status}: Order {$order->code} - " . $smsException->getMessage());
        }
    }
}