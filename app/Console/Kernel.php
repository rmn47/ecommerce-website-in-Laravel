<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ForceClearRouteCache::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        // Schedule task to cancel abandoned orders
        $schedule->call(function () {
            $timeout = now()->subMinutes(30); // 30-minute timeout for payment completion
            $abandoned_orders = Order::where('payment_status', 'pending')
                ->where('created_at', '<', $timeout)
                ->get();
    
            foreach ($abandoned_orders as $order) {
                Log::info("Cancelling abandoned order: Order {$order->code}");
    
                // Restock products
                foreach ($order->orderDetails as $orderDetail) {
                    $product = $orderDetail->product;
                    if ($product->digital != 1) {
                        $product_stock = $product->stocks->where('variant', $orderDetail->variation)->first();
                        $product_stock->qty += $orderDetail->quantity;
                        $product_stock->save();
                    }
                }
    
                // Update order status
                $order->payment_status = 'cancelled';
                $order->delivery_status = 'cancelled';
                $order->save();
    
                // Notify user
                EmailUtility::order_email($order, 'cancelled');
                $orderController = new OrderController();
                $orderController->sendHardcodedEmail($order, 'cancelled');
    
                // Send SMS notification
                try {
                    $serverUrl = "msg.msgclub.net";
                    $authKey = '73956ead71c21196e44ba9bf1523f8a'; // Replace with your actual auth key
                    $msg = "Dear {$order->user->name}, your order {$order->code} has been cancelled due to payment timeout on " . env('APP_NAME') . ".";
    
                    $postData = [
                        'mobileNumbers' => json_decode($order->shipping_address)->phone,
                        'smsContent' => $msg,
                        'senderId' => 'DEMOOS',
                        'routeId' => '1',
                        "smsContentType" => 'english'
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
                        Log::error("SMS sending failed for abandoned order cancellation: Order {$order->code} - " . curl_error($ch));
                    } else {
                        $obj = json_decode($output);
                        if ($obj->responseCode == 3001) {
                            Log::info("SMS sent successfully to {$order->user->phone} for abandoned order cancellation: Order {$order->code}");
                        } else {
                            Log::error("SMS API response error for abandoned order cancellation: Order {$order->code} - " . $output);
                        }
                    }
                    curl_close($ch);
                } catch (\Exception $smsException) {
                    Log::error("Failed to send SMS notification for abandoned order cancellation: Order {$order->code} - " . $smsException->getMessage());
                }
            }
    
            // Optionally, delete abandoned combined orders if all orders are cancelled
            $abandoned_combined_orders = CombinedOrder::whereDoesntHave('orders', function ($query) {
                $query->where('payment_status', '!=', 'cancelled');
            })->where('created_at', '<', $timeout)->get();
    
            foreach ($abandoned_combined_orders as $combined_order) {
                $combined_order->delete();
                Log::info("Deleted abandoned combined order: CombinedOrder {$combined_order->id}");
            }
        })->everyMinute(); // Adjust frequency as needed (e.g., everyFiveMinutes())
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
