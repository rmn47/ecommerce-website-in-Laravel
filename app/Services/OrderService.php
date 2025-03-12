<?php
namespace App\Services;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\ProductStock;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Utility\NotificationUtility;
use App\Utility\SmsUtility;
use App\Utility\EmailUtility;
use Illuminate\Support\Facades\Mail;


class OrderService{

    public function handle_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();

        if ($request->status == 'cancelled' && $order->payment_type == 'wallet') {
            $user = User::where('id', $order->user_id)->first();
            $user->balance += $order->grand_total;
            $user->save();
        }

        foreach ($order->orderDetails as $key => $orderDetail) {

            $orderDetail->delivery_status = $request->status;
            $orderDetail->save();

            if ($request->status == 'cancelled') {
                product_restock($orderDetail);
            }

            if (addon_is_activated('affiliate_system') && auth()->user()->user_type == 'admin') {
                if (($request->status == 'delivered' || $request->status == 'cancelled') &&
                    $orderDetail->product_referral_code
                ) {

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
        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'delivery_status_change')->first()->status == 1) {
            try {
                SmsUtility::delivery_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {

            }
        }

        //sends Notifications to user
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->delivery_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('delivery_boy')) {
            if (auth()->user()->user_type == 'delivery_boy') {
                $deliveryBoyController = new DeliveryBoyController;
                $deliveryBoyController->store_delivery_history($order);
            }
        }
    }

    public function handle_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (auth()->user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', auth()->user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            calculateCommissionAffilationClubPoint($order);
        }

        //sends Notifications to user
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->payment_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
            try {
                SmsUtility::payment_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {

            }
        }
        return 1;
    
    }
    
    public function sendOrderEmails($orderId, $status)
    {
        $order = Order::findOrFail($orderId);

        // Email 1: Using database template via EmailUtility
        EmailUtility::order_email($order, $status);

        // Email 2: Using hardcoded Blade template
        $this->sendHardcodedEmail($order, $status);
    }

    protected function sendHardcodedEmail($order, $status)
    {
        // Define email content based on status
        switch ($status) {
            case 'confirmed':
                $subject = env('APP_NAME') . " - Order Confirmed";
                $header = translate('Great news!');
                $message = translate('Your order has been confirmed. We’re preparing it for you.');
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
            case 'refund_request':
                $subject = env('APP_NAME') . " - Refund Request Received";
                $header = translate('Refund request received');
                $message = translate('We’ve received your refund request and are processing it.');
                break;
            case 'refund_approved':
                $subject = env('APP_NAME') . " - Refund Approved";
                $header = translate('Refund approved');
                $message = translate('Your refund request has been approved. Expect it soon.');
                break;
            case 'refund_rejected':
                $subject = env('APP_NAME') . " - Refund Rejected";
                $header = translate('Refund update');
                $message = translate('Your refund request has been rejected.');
                break;
            case 'refund_processed':
                $subject = env('APP_NAME') . " - Refund Processed";
                $header = translate('Refund processed');
                $message = translate('Your refund has been processed successfully.');
                break;
            case 'refund_cancelled':
                $subject = env('APP_NAME') . " - Refund Cancelled";
                $header = translate('Refund cancelled');
                $message = translate('Your refund request has been cancelled.');
                break;
            case 'refund_failed':
                $subject = env('APP_NAME') . " - Refund Failed";
                $header = translate('Refund issue');
                $message = translate('Your refund attempt failed. Please contact support.');
                break;
            case 'refund_completed':
                $subject = env('APP_NAME') . " - Refund Completed";
                $header = translate('Refund completed');
                $message = translate('Your refund has been completed successfully.');
                break;
            default:
                $subject = env('APP_NAME') . " - Order Update";
                $header = translate('Order update');
                $message = translate('Your order status has been updated.');
                break;
        }

        // Prepare data for the hardcoded template
        $data = [
            'order' => $order,
            'subject' => $subject,
            'header' => $header,
            'message' => $message,
            'status' => $status,
        ];

        // Send the email using the hardcoded Blade template
        try {
            Mail::send('emails.order_notification', $data, function ($message) use ($order, $subject) {
                $message->to($order->user->email)
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->subject($subject);
            });
            \Log::info("Hardcoded email sent successfully to {$order->user->email} for status: {$status}");
        } catch (\Exception $e) {
            \Log::error("Failed to send hardcoded email for status {$status}: " . $e->getMessage());
        }
    }

}