<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\MailManager;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Utility\SmsUtility;
use Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        
        $phone = "+{$request['country_code']}{$request['phone']}";
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->email)->first();
            if ($user != null) {
                $user->verification_code = rand(100000,999999);
                $user->save();
                
                $emailTemplate = EmailTemplate::whereIdentifier('password_reset_email_to_all')->first();
                $emailSubject = $emailTemplate->subject;
                $emailSubject = str_replace('[[store_name]]', get_setting('site_name'), $emailSubject);

                $email_body = $emailTemplate->default_text;
                $email_body = str_replace('[[user_email]]', $user->email, $email_body);
                $email_body = str_replace('[[code]]', $user->verification_code, $email_body);
                $email_body = str_replace('[[store_name]]', get_setting('site_name'), $email_body);
                
                $array['subject'] = $emailSubject;
                $array['content'] = $email_body;
                Mail::to($user->email)->queue(new MailManager($array));

                return view('auth.'.get_setting('authentication_layout_select').'.reset_password');
            }
            else {
                flash(translate('No account exists with this email'))->error();
                return back();
            }
        }
        else{
            $user = User::where('phone', $phone)->first();
            if ($user != null) {
                $user->verification_code = rand(100000,999999);
                $user->save();
                SmsUtility::password_reset($user);
                return view('otp_systems.frontend.auth.'.get_setting('authentication_layout_select').'.reset_with_phone');
            }
            else {
                flash(translate('No account exists with this phone number'))->error();
                return back();
            }
        }
    }
}
