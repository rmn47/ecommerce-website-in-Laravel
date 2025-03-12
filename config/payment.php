<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configurations
    |--------------------------------------------------------------------------
    |
    | This file contains all payment gateway configurations used by the application.
    | Each gateway has its own configuration section.
    |
    */

    'razorpay' => [
        'key' => env('RAZOR_KEY', ''),
        'secret' => env('RAZOR_SECRET', ''),
        'currency' => env('RAZOR_CURRENCY', 'INR'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY', ''),
        'secret' => env('STRIPE_SECRET', ''),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
    ],

    'iyzico' => [
        'api_key' => env('IYZICO_API_KEY', ''),
        'secret_key' => env('IYZICO_SECRET_KEY', ''),
        'currency_code' => env('IYZICO_CURRENCY_CODE', 'TRY'),
    ],

    'bkash' => [
        'app_key' => env('BKASH_CHECKOUT_APP_KEY', ''),
        'app_secret' => env('BKASH_CHECKOUT_APP_SECRET', ''),
        'username' => env('BKASH_CHECKOUT_USER_NAME', ''),
        'password' => env('BKASH_CHECKOUT_PASSWORD', ''),
    ],

    'phonepe' => [
        'merchant_id' => env('PHONEPE_MERCHANT_ID', ''),
        'salt_key' => env('PHONEPE_SALT_KEY', ''),
        'salt_index' => env('PHONEPE_SALT_INDEX', 1),
    ],

    'instamojo' => [
        'api_key' => env('IM_API_KEY', ''),
        'auth_token' => env('IM_AUTH_TOKEN', ''),
    ],

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY', ''),
        'secret_key' => env('PAYSTACK_SECRET_KEY', ''),
    ],

    'paytm' => [
        'environment' => env('PAYTM_ENVIRONMENT', 'production'),
        'merchant_id' => env('PAYTM_MERCHANT_ID', ''),
        'merchant_key' => env('PAYTM_MERCHANT_KEY', ''),
        'merchant_website' => env('PAYTM_MERCHANT_WEBSITE', 'DEFAULT'),
        'channel' => env('PAYTM_CHANNEL', 'WEB'),
        'industry_type' => env('PAYTM_INDUSTRY_TYPE', 'Retail'),
    ],

    'sslcommerz' => [
        'store_id' => env('SSLCZ_STORE_ID', ''),
        'store_password' => env('SSLCZ_STORE_PASSWD', ''),
    ],

    'payhere' => [
        'merchant_id' => env('PAYHERE_MERCHANT_ID', ''),
        'secret' => env('PAYHERE_SECRET', ''),
        'currency' => env('PAYHERE_CURRENCY', ''),
    ],

    'ngenius' => [
        'outlet_id' => env('NGENIUS_OUTLET_ID', ''),
        'api_key' => env('NGENIUS_API_KEY', ''),
        'currency' => env('NGENIUS_CURRENCY', 'AED'),
    ],

    'voguepay' => [
        'merchant_id' => env('VOGUE_MERCHANT_ID', ''),
    ],

    'aamarpay' => [
        'store_id' => env('AAMARPAY_STORE_ID', ''),
        'signature_key' => env('AAMARPAY_SIGNATURE_KEY', ''),
    ],

    'flutterwave' => [
        'public_key' => env('FLW_PUBLIC_KEY', ''),
        'secret_key' => env('FLW_SECRET_KEY', ''),
        'secret_hash' => env('FLW_SECRET_HASH', ''),
    ],

    'payfast' => [
        'merchant_id' => env('PAYFAST_MERCHANT_ID', ''),
        'merchant_key' => env('PAYFAST_MERCHANT_KEY', ''),
    ],
]; 