<?php

namespace App\Utility;

use App\Models\OtpConfiguration;
use App\Utility\MimoUtility;
use Twilio\Rest\Client;

class SendSMSUtility
{
    public static function sendSMS($to, $from, $text, $template_id)
    {
        if (OtpConfiguration::where('type', 'nexmo')->first()->value == 1) {
            $api_key = env("NEXMO_KEY"); //put ssl provided api_token here
            $api_secret = env("NEXMO_SECRET"); // put ssl provided sid here

            $params = [
                "api_key" => $api_key,
                "api_secret" => $api_secret,
                "from" => $from,
                "text" => $text,
                "to" => $to
            ];

            $url = "https://rest.nexmo.com/sms/json";
            $params = json_encode($params);

            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } elseif (OtpConfiguration::where('type', 'twillo')->first()->value == 1) {
            $sid = env("TWILIO_SID"); // Your Account SID from www.twilio.com/console
            $token = env("TWILIO_AUTH_TOKEN"); // Your Auth Token from www.twilio.com/console
            $type = env("TWILLO_TYPE"); // sms type

            $client = new Client($sid, $token);
            try {
                 $client->messages->create(
                    $type == 1? $to : "whatsapp:".$to, // Text this number
                    array(
                        'from' =>  $type == 1? env('VALID_TWILLO_NUMBER') : "whatsapp:".env('VALID_TWILLO_NUMBER'), // From a valid Twilio number
                        'body' => $text
                    )
                );
            } catch (\Exception $e) {
            }
            
        } elseif (OtpConfiguration::where('type', 'msgclub')->first()->value == 1) {
            // MSGClub SMS Gateway
            $serverUrl = env('MSGCLUB_SERVER_URL');
            $authKey = env('MSGCLUB_AUTH_KEY');
            $senderId = env('MSGCLUB_SENDER_ID');
            $routeId = env('MSGCLUB_ROUTE_ID');
            $smsContentType = env('MSGCLUB_LANGUAGE');
            
            $postData = [
                'mobileNumbers' => $to,
                'smsContent' => $text,
                'senderId' => $senderId,
                'routeId' => $routeId,
                "smsContentType" => $smsContentType
            ];
            
            // Add template ID if provided and entity ID is configured
            if (!empty($template_id) && !empty(env('MSGCLUB_ENTITY_ID'))) {
                $postData['entityId'] = env('MSGCLUB_ENTITY_ID');
                $postData['templateId'] = $template_id;
            }
            
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
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            return $response;
        } elseif (OtpConfiguration::where('type', 'ssl_wireless')->first()->value == 1) {
            $token = env("SSL_SMS_API_TOKEN"); //put ssl provided api_token here
            $sid = env("SSL_SMS_SID"); // put ssl provided sid here

            $params = [
                "api_token" => $token,
                "sid" => $sid,
                "msisdn" => $to,
                "sms" => $text,
                "csms_id" => date('dmYhhmi') . rand(10000, 99999)
            ];

            $url = env("SSL_SMS_URL");
            $params = json_encode($params);

            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));

            $response = curl_exec($ch);

            curl_close($ch);

            return $response;
        } elseif (OtpConfiguration::where('type', 'fast2sms')->first()->value == 1) {

            if (strpos($to, '+91') !== false) {
                $to = substr($to, 3);
            }

            if (env("ROUTE") == 'dlt_manual') {
                $fields = array(
                    "sender_id" => env("SENDER_ID"),
                    "message" => $text,
                    "template_id" => $template_id,
                    "entity_id" => env("ENTITY_ID"),
                    "language" => env("LANGUAGE"),
                    "route" => env("ROUTE"),
                    "numbers" => $to,
                );
            } else {
                $fields = array(
                    "sender_id" => env("SENDER_ID"),
                    "message" => $text,
                    "language" => env("LANGUAGE"),
                    "route" => env("ROUTE"),
                    "numbers" => $to,
                );
            }


            $auth_key = env('AUTH_KEY');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "authorization: $auth_key",
                    "accept: */*",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return $response;
        } elseif (OtpConfiguration::where('type', 'mimo')->first()->value == 1) {
            $token = MimoUtility::getToken();

            MimoUtility::sendMessage($text, $to, $token);
            MimoUtility::logout($token);
        } elseif (OtpConfiguration::where('type', 'mimsms')->first()->value == 1) {
            $url = env('MIM_BASE_URL') . "/smsapi";
            $data = [
                "api_key" => env('MIM_API_KEY'),
                "type" => "text",
                "contacts" => $to,
                "senderid" => env('MIM_SENDER_ID'),
                "msg" => $text,
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } elseif (OtpConfiguration::where('type', 'msegat')->first()->value == 1) {
            $url = "https://www.msegat.com/gw/sendsms.php";
            $data = [
                "apiKey" => env('MSEGAT_API_KEY'),
                "numbers" => $to,
                "userName" => env('MSEGAT_USERNAME'),
                "userSender" => env('MSEGAT_USER_SENDER'),
                "msg" => $text
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } elseif (OtpConfiguration::where('type', 'sparrow')->first()->value == 1) {
            $url = "http://api.sparrowsms.com/v2/sms/";

            $args = http_build_query(array(
                "token" => env('SPARROW_TOKEN'),
                "from" => env('MESSGAE_FROM'),
                "to" => $to,
                "text" => $text
            ));
            # Make the call using API.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // Response
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } elseif (OtpConfiguration::where('type', 'zender')->first()->value == 1) {
            if (empty(env('ZENDER_SERVICE')) || env('ZENDER_SERVICE') < 2) {
                if (!empty(env('ZENDER_DEVICE'))) {
                    $mode = "devices";
                } else {
                    $mode = "credits";
                }

                if ($mode == "devices") {
                    $params = [
                        "secret" => env('ZENDER_APIKEY'),
                        "mode" => "devices",
                        "device" => env('ZENDER_DEVICE'),
                        "phone" => $to,
                        "message" => $text,
                        "sim" => env('ZENDER_SIM') < 2 ? 1 : 2
                    ];
                } else {
                    $params = [
                        "secret" => env('ZENDER_APIKEY'),
                        "mode" => "credits",
                        "gateway" => env('ZENDER_GATEWAY'),
                        "phone" => $to,
                        "message" => $text
                    ];
                }

                $apiurl = env('ZENDER_SITEURL') . "/api/send/sms";
            } else {
                $params = [
                    "secret" => env('ZENDER_APIKEY'),
                    "account" => env('ZENDER_WHATSAPP'),
                    "type" => "text",
                    "recipient" => $to,
                    "message" => $text
                ];

                $apiurl = env('ZENDER_SITEURL') . "/api/send/whatsapp";
            }

            $args = http_build_query($params);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiurl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // Response
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }
        return true;
    }

    public static function verifyOtp($phone, $otp)
    {
        $user = User::where('phone', $phone)->first();
        if ($user != null) {
            if ($user->verification_code == $otp) {
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->verification_code = null;
                $user->save();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function sendOtp($phone)
    {
        $user = User::where('phone', $phone)->first();
        if ($user != null) {
            $otp = rand(100000, 999999);
            $user->verification_code = $otp;
            $user->save();
            sendSMS($phone, env('APP_NAME'), translate('Your OTP code is ') . $otp);
            return true;
        } else {
            return false;
        }
    }

    public static function product_purchase($phone, $otp)
    {
        $user = User::where('phone', $phone)->first();
        if ($user != null) {
            $otp = rand(100000, 999999);
            $user->verification_code = $otp;
            $user->save();
            sendSMS($phone, env('APP_NAME'), translate('Your OTP code is ') . $otp);
            return true;
        } else {
            return false;
        }
    }

    public static function reset_password($phone, $otp)
    {
        $user = User::where('phone', $phone)->first();
        if ($user != null) {
            $otp = rand(100000, 999999);
            $user->verification_code = $otp;
            $user->save();
            sendSMS($phone, env('APP_NAME'), translate('Your OTP code is ') . $otp);
            return true;
        } else {
            return false;
        }
    }

    public static function two_factor_reset($phone)
    {
        $user = User::where('phone', $phone)->first();
        if ($user != null) {
            $otp = rand(100000, 999999);
            $user->verification_code = $otp;
            $user->save();
            sendSMS($phone, env('APP_NAME'), translate('Your OTP code is ') . $otp);
            return true;
        } else {
            return false;
        }
    }

    public static function delivery_status_change($phone, $order)
    {
        sendSMS($phone, env('APP_NAME'), translate('Your delivery status has been updated to ') . $order->delivery_status . ' ' . translate('for Order code') . ' : ' . $order->code);
        return true;
    }

    public static function payment_status_change($phone, $order)
    {
        sendSMS($phone, env('APP_NAME'), translate('Your payment status has been updated to ') . $order->payment_status . ' ' . translate('for Order code') . ' : ' . $order->code);
        return true;
    }

    public static function assign_delivery_boy($phone, $code)
    {
        sendSMS($phone, env('APP_NAME'), translate('You are assigned to delivery an order. ') . ' ' . translate('Order code') . ' : ' . $code);
        return true;
    }

    public static function order_placement_message($phone, $order)
    {
        sendSMS($phone, env('APP_NAME'), translate('Your order has been placed successfully.') . ' ' . translate('Order code') . ' : ' . $order->code);
        return true;
    }
}
