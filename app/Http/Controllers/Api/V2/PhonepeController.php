<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\CombinedOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Config;

class PhonepeController extends Controller
{
    /**
     * Load and validate PhonePe configuration.
     *
     * @return array
     * @throws \Exception
     */
    private function loadPhonepeConfig()
    {
        // Use config() with fallback to env()
        $merchantId = config('payment.phonepe.merchant_id', env('PHONEPE_MERCHANT_ID', 'M227A4X7RJY4Z'));
        $salt_key = config('payment.phonepe.salt_key', env('PHONEPE_SALT_KEY', '7e6cc387-5b6c-4b17-a299-5d44ee2952c4'));
        $salt_index = config('payment.phonepe.salt_index', env('PHONEPE_SALT_INDEX', 1));

        // Log raw env values
        \Log::info('Raw Env Values via env(): ' . json_encode([
            'PHONEPE_MERCHANT_ID' => env('PHONEPE_MERCHANT_ID'),
            'PHONEPE_SALT_KEY' => env('PHONEPE_SALT_KEY'),
            'PHONEPE_SALT_INDEX' => env('PHONEPE_SALT_INDEX', 1)
        ]));

        // Log loaded variables
        \Log::info('Loaded Config Variables: ' . json_encode([
            'merchantId' => $merchantId,
            'salt_key' => $salt_key,
            'salt_index' => $salt_index
        ]));

        // Check Config facade as a fallback
        $configMerchantId = Config::get('app.phonepe_merchant_id');
        $configSaltKey = Config::get('app.phonepe_salt_key');
        $configSaltIndex = Config::get('app.phonepe_salt_index', 1);
        \Log::info('Config Facade Values: ' . json_encode([
            'phonepe_merchant_id' => $configMerchantId,
            'phonepe_salt_key' => $configSaltKey,
            'phonepe_salt_index' => $configSaltIndex
        ]));

        // Test if .env file is readable
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContents = file_get_contents($envPath);
            \Log::info('Reading .env file directly: ' . (strpos($envContents, 'PHONEPE_MERCHANT_ID') !== false ? 'Found PHONEPE_MERCHANT_ID' : 'PHONEPE_MERCHANT_ID not found'));
            \Log::info('.env file contents (partial): ' . substr($envContents, 0, 200)); // Log first 200 chars for safety
        } else {
            \Log::error('.env file not found at: ' . $envPath);
        }

        // Explicitly check each variable and log which is missing
        $missing = [];
        if (!$merchantId) $missing[] = 'PHONEPE_MERCHANT_ID';
        if (!$salt_key) $missing[] = 'PHONEPE_SALT_KEY';
        if (!$salt_index) $missing[] = 'PHONEPE_SALT_INDEX';

        if (!empty($missing)) {
            \Log::error('PhonePe configuration incomplete. Missing: ' . implode(', ', $missing));
            throw new \Exception('PhonePe configuration incomplete. Missing: ' . implode(', ', $missing));
        }

        return [
            'merchantId' => $merchantId,
            'salt_key' => $salt_key,
            'salt_index' => $salt_index,
            'sandbox_mode' => get_setting('phonepe_sandbox')
        ];
    }

    /**
     * Validate request input and generate transaction ID.
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    private function validateInputAndSetTransactionId(Request $request)
    {
        \Log::info('PhonePe Pay Request Data: ' . json_encode($request->all()));
        $paymentType = $request->payment_type;

        if (!$paymentType) {
            \Log::error('Payment type is required');
            throw new \Exception('Payment type is required');
        }

        $merchantUserId = $request->user_id;
        $amount = $request->amount;
        $userId = $request->user_id;

        switch ($paymentType) {
            case 'cart_payment':
                $combined_order = CombinedOrder::find($request->combined_order_id);
                if (!$combined_order) {
                    \Log::error('Combined Order not found for ID: ' . $request->combined_order_id);
                    throw new \Exception('Combined order not found');
                }
                $amount = $combined_order->grand_total;
                $merchantTransactionId = $paymentType . '-' . $combined_order->id . '-' . $userId . '-' . time();
                break;

            case 'order_re_payment':
                $order = Order::find($request->order_id);
                if (!$order) {
                    \Log::error('Order not found for ID: ' . $request->order_id);
                    throw new \Exception('Order not found');
                }
                $amount = $order->grand_total;
                $merchantTransactionId = $paymentType . '-' . $order->id . '-' . $userId . '-' . time();
                break;

            case 'wallet_payment':
                $merchantTransactionId = $paymentType . '-' . $userId . '-' . $userId . '-' . time();
                break;

            case 'seller_package_payment':
            case 'customer_package_payment':
                $merchantTransactionId = $paymentType . '-' . $request->package_id . '-' . $userId . '-' . time();
                break;

            default:
                \Log::error('Invalid payment type: ' . $paymentType);
                throw new \Exception('Invalid payment type: ' . $paymentType);
        }

        if (!is_numeric($amount) || $amount <= 0) {
            \Log::error('Invalid amount for payment: ' . $amount);
            throw new \Exception('Invalid order amount');
        }

        return [
            'merchantUserId' => $merchantUserId,
            'amount' => $amount,
            'merchantTransactionId' => $merchantTransactionId
        ];
    }

    /**
     * Build the PhonePe request payload.
     *
     * @param array $config
     * @param array $transactionData
     * @param Request $request
     * @return array
     */
    private function buildPayload($config, $transactionData, Request $request)
    {
        $post_field = [
            'merchantId' => $config['merchantId'],
            'merchantTransactionId' => $transactionData['merchantTransactionId'],
            'merchantUserId' => $transactionData['merchantUserId'],
            'amount' => (int) ($transactionData['amount'] * 100),
            'redirectUrl' => route('api.phonepe.redirecturl'),
            'redirectMode' => 'POST',
            'callbackUrl' => route('api.phonepe.callbackUrl'),
            'mobileNumber' => $request->mobile_number ?? "9999999999",
            'paymentInstrument' => [
                'type' => 'PAY_PAGE'
            ],
        ];

        \Log::info('PhonePe Payload Data: ' . json_encode($post_field));
        return $post_field;
    }

    /**
     * Generate the X-VERIFY header signature.
     *
     * @param array $payload
     * @param string $salt_key
     * @param string $salt_index
     * @return string
     */
    private function generateSignature($payload, $salt_key, $salt_index)
    {
        $encodedPayload = base64_encode(json_encode($payload));
        $hashedkey = hash('sha256', $encodedPayload . "/pg/v1/pay" . $salt_key) . '###' . $salt_index;

        \Log::info('Signature Generation: ' . json_encode([
            'encoded_payload' => $encodedPayload,
            'hashedkey' => $hashedkey
        ]));

        return $hashedkey;
    }

    /**
     * Send the request to PhonePe API.
     *
     * @param string $base_url
     * @param array $payload
     * @param string $hashedkey
     * @return array
     * @throws \Exception
     */
    private function sendPhonepeRequest($base_url, $payload, $hashedkey)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-VERIFY: ' . $hashedkey,
            'accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['request' => base64_encode(json_encode($payload))]));

        \Log::info('PhonePe API Request: ' . json_encode([
            'url' => $base_url,
            'headers' => [
                'Content-Type: application/json',
                'X-VERIFY: ' . $hashedkey,
                'accept: application/json'
            ],
            'body' => json_encode(['request' => base64_encode(json_encode($payload))])
        ]));

        $response = curl_exec($ch);
        if ($response === false) {
            \Log::error('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            throw new \Exception('Payment request failed');
        }
        \Log::info('PhonePe API Raw Response: ' . $response);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Initiate a PhonePe payment.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function pay(Request $request)
    {
        try {
            // Step 1: Load configuration
            $config = $this->loadPhonepeConfig();

            // Step 2: Validate input and set transaction ID
            $transactionData = $this->validateInputAndSetTransactionId($request);

            // Step 3: Build payload
            $payload = $this->buildPayload($config, $transactionData, $request);

            // Step 4: Generate signature
            $hashedkey = $this->generateSignature($payload, $config['salt_key'], $config['salt_index']);

            // Step 5: Send API request
            $base_url = ($config['sandbox_mode'] == 1) 
                ? "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay" 
                : "https://api.phonepe.com/apis/hermes/pg/v1/pay";
            $response = $this->sendPhonepeRequest($base_url, $payload, $hashedkey);

            if (!$response || !isset($response['success']) || !$response['success'] || !isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
                \Log::error('PhonePe Response Error: ' . json_encode($response));
                return response()->json(['error' => 'Payment initiation failed', 'response' => json_encode($response)], 400);
            }

            return Redirect::to($response['data']['instrumentResponse']['redirectInfo']['url']);
        } catch (\Exception $e) {
            \Log::error('Payment Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], $e->getMessage() === 'Combined order not found' || $e->getMessage() === 'Order not found' ? 404 : 400);
        }
    }

    /**
     * Handle PhonePe redirect URL after payment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function phonepe_redirecturl(Request $request)
    {
        $payment_type = explode("-", $request['transactionId']);
        // auth()->login(User::findOrFail($payment_type[2]));
        // dd($payment_type[0], $payment_type[1], $request['merchantId'], $request['transactionId'], $request->all());

        if ($request['code'] == 'PAYMENT_SUCCESS') {
            return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
        }
        return response()->json(['result' => false, 'message' => translate("Payment is failed")]);
    }

    /**
     * Handle PhonePe callback URL.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function phonepe_callbackUrl(Request $request)
    {
        $res = $request->all();
        $response = $res['response'];
        $decoded_response = json_decode(base64_decode($response));

        \Log::info('PhonePe Callback Response: ' . json_encode($decoded_response));

        $payment_type = explode("-", $decoded_response->data->merchantTransactionId);
        $amount = $decoded_response->data->amount / 100;

        if ($decoded_response->code == 'PAYMENT_SUCCESS') {
            if ($payment_type[0] == 'cart_payment') {
                checkout_done($payment_type[1], json_encode($decoded_response->data));
            } elseif ($payment_type[0] == 'order_re_payment') {
                order_re_payment_done($payment_type[1], 'phonepe', json_encode($decoded_response->data));
            } elseif ($payment_type[0] == 'wallet_payment') {
                wallet_payment_done($payment_type[2], $amount, 'phonepe', json_encode($decoded_response->data));
            } elseif ($payment_type[0] == 'seller_package_payment') {
                seller_purchase_payment_done($payment_type[2], $payment_type[1], 'phonepe', json_encode($decoded_response->data));
            } elseif ($payment_type[0] == 'customer_package_payment') {
                customer_purchase_payment_done($payment_type[2], $payment_type[1], 'phonepe', json_encode($decoded_response->data));
            }
        } else {
            \Log::warning('Payment failed in callback for Transaction ID: ' . $decoded_response->data->merchantTransactionId);
        }

        return response()->json(['status' => 'success']);
    }
}