<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    use VerifiesEmails;

    protected $redirectTo = '/';

    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function otp(Request $request)
    {
        $mobile = $request->input('mb');
        $serverUrl = env('MSGCLUB_SERVER_URL');
        $authKey = env('MSGCLUB_AUTH_KEY');
        $msg = rand(1005, 9999);

        $postData = [
            'mobileNumbers' => $mobile,
            'smsContent' => "Your 1 Time OTP for " . env('APP_NAME', 'MeFashion') . " is " . $msg,
            'senderId' => env('MSGCLUB_SENDER_ID', 'DEMOOS'),
            'routeId' => env('MSGCLUB_ROUTE_ID', '1'),
            "smsContentType" => env('MSGCLUB_LANGUAGE', 'english')
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
            curl_close($ch);
            return response()->json([
                'result' => '0',
                'message' => 'cURL Error: ' . curl_error($ch)
            ]);
        }
        curl_close($ch);

        $obj = json_decode($output);

        if ($obj->responseCode == 3001) {
            $userExists = DB::table('users')->where('phone', '=', $mobile)->exists();

            if (!$userExists) {
                $res = DB::table('users')->insert([
                    'phone' => $mobile,
                    'verification_code' => $msg,
                    'isCodeVerified' => 'No',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                return response()->json([
                    'result' => $res ? '1' : '0',
                    'message' => $res ? 'OTP sent successfully' : 'Failed to save OTP'
                ]);
            } else {
                $ups = DB::table('users')->where('phone', $mobile)->update([
                    'verification_code' => $msg,
                    'isCodeVerified' => 'No',
                    'updated_at' => Carbon::now()
                ]);

                return response()->json([
                    'result' => $ups ? '1' : '0',
                    'message' => $ups ? 'OTP sent successfully' : 'Failed to update OTP'
                ]);
            }
        } else {
            return response()->json([
                'result' => '0',
                'message' => 'Failed to send OTP via SMS API'
            ]);
        }
    }

    public function vOtp(Request $request)
    {
        $mobile = $request->input('mb');
        $otp = $request->input('otp');

        $user = User::where('phone', '=', $mobile)->first();

        if (!$user) {
            return response()->json([
                'result' => '0',
                'message' => 'User not found'
            ]);
        }

        if ($user->verification_code == $otp) {
            $dt = Carbon::now()->toDateTimeString();
            $updated = DB::table('users')
                ->where('phone', $mobile)
                ->update([
                    'email_verified_at' => $dt,
                    'isCodeVerified' => 'Yes',
                    'updated_at' => $dt
                ]);

            if ($updated) {
                Auth::login($user);

                // Check if user is fully registered (all required fields present)
                $isFullyRegistered = $user->user_type && $user->name;

                if ($isFullyRegistered) {
                    return response()->json([
                        'result' => '1',
                        'user_type' => $user->user_type,
                        'exists' => true,
                        'redirect' => $user->user_type === 'seller' ? route('dashboard') : route('home'),
                        'message' => 'OTP verified successfully. Redirecting to ' . ($user->user_type === 'seller' ? 'dashboard.' : 'home.')
                    ]);
                    
                }

                // Incomplete profile or new user
                return response()->json([
                    'result' => '1',
                    'user_type' => null,
                    'exists' => false,
                    'mobile' => $mobile,
                    'message' => ''
                ]);
            } else {
                return response()->json([
                    'result' => '2',
                    'message' => 'Failed to update verification status'
                ]);
            }
        } else {
            return response()->json([
                'result' => '0',
                'message' => 'Invalid OTP'
            ]);
        }
    }
}