<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Utility\EmailUtility;
use Hash;
use Illuminate\Support\Facades\Storage;

class WholesellerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_all_customers'])->only('index');
        $this->middleware(['permission:add_customer'])->only('create', 'store');
        $this->middleware(['permission:login_as_customer'])->only('login');
        $this->middleware(['permission:ban_customer'])->only('ban');
        $this->middleware(['permission:mark_customer_suspected'])->only('suspicious');
        $this->middleware(['permission:delete_customer'])->only('destroy');
        $this->middleware(['permission:edit_customer'])->only('edit', 'update');
    }

    public function index(Request $request)
    {
        $sort_search = $request->search ?? null;
        $verification_status = $request->verification_status ?? null;
        $users = User::where('user_type', 'customer')->where('is_wholeseller', 1)->orderBy('created_at', 'desc');

        if ($verification_status) {
            $users = $verification_status == 'verified' ? $users->whereNotNull('email_verified_at') : $users->whereNull('email_verified_at');
        }
        if ($sort_search) {
            $users->where(function ($q) use ($sort_search) {
                $q->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%');
            });
        }
        $users = $users->paginate(15);
        $wholeseller = true; // Changed to boolean for consistency with view condition
        return view('backend.customer.customers.index', compact('users', 'sort_search', 'verification_status', 'wholeseller'));
    }

    public function create()
    {
        $wholeseller = true; // Explicitly true for wholeseller creation
        return view('backend.customer.customers.create', compact('wholeseller'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:organization,seller,customer,wholeseller',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255',
            'address' => 'required|string|max:500',
            'gst_no' => 'nullable|string|max:50',
            'drug_license_no' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_wholeseller' => 'sometimes|in:0,1',
        ]);

        if (User::where('email', $request->email)->first()) {
            flash(translate('Email already exists.'))->error();
            return back();
        }

        if (User::where('phone', '+'.$request->country_code.$request->phone)->first()) {
            flash(translate('Phone already exists.'))->error();
            return back();
        }

        $password = substr(hash('sha512', rand()), 0, 8);
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('uploads/users', 'public');
        }

        $user = User::create([
            'user_type' => $request->user_type,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => '+'.$request->country_code.$request->phone,
            'address' => $request->address,
            'gst_no' => $request->gst_no,
            'drug_license_no' => $request->drug_license_no,
            'photo' => $photoPath,
            'is_wholeseller' => 1, // Always 1 for wholesellers
            'password' => Hash::make($password),
            'verification_code' => rand(100000, 999999),
        ]);

        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            try {
                EmailUtility::customer_registration_email('registration_from_system_email_to_customer', $user, $password);
                if (get_setting('email_verification') != 1) {
                    $user->email_verified_at = now();
                    $user->save();
                    offerUserWelcomeCoupon();
                } else {
                    EmailUtility::email_verification($user, 'customer');
                }
            } catch (\Exception $e) {
                $user->delete();
                flash(translate('Registration failed. Please try again later.'))->error();
                return back();
            }
        } elseif (addon_is_activated('otp_system')) {
            // Uncomment and adjust if OTP is needed
            // $otpController = new OTPVerificationController;
            // $otpController->account_opening($user, $password);
        }

        if (get_email_template_data('customer_reg_email_to_admin', 'status') == 1) {
            try {
                EmailUtility::customer_registration_email('customer_reg_email_to_admin', $user, null);
            } catch (\Exception $e) {}
        }

        flash(translate('Wholeseller registration successful.'))->success();
        return back();
    }

    public function edit($id)
    {
        try {
            $user = User::findOrFail(decrypt($id));
            $wholeseller = true; // Always true for wholeseller edit
            return view('backend.customer.customers.create', compact('user', 'wholeseller'));
        } catch (\Exception $e) {
            flash(translate('Invalid wholeseller ID or decryption failed.'))->error();
            return redirect()->route('wholesellers.index');
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'user_type' => 'required|in:organization,seller,customer,wholeseller',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'address' => 'required|string|max:500',
            'gst_no' => 'nullable|string|max:50',
            'drug_license_no' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_wholeseller' => 'sometimes|in:0,1',
        ]);

        $data = $request->only(['user_type', 'name', 'phone', 'email', 'address', 'gst_no', 'drug_license_no', 'is_wholeseller']);
        $data['is_wholeseller'] = 1; // Ensure it remains 1 for wholesellers

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('uploads/users', 'public');
        }

        $user->update($data);
        flash(translate('Wholeseller updated successfully'))->success();
        return redirect()->route('wholesellers.index');
    }

    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        $customer->customer_products()->delete();
        User::destroy($id);
        flash(translate('Wholeseller has been deleted successfully'))->success();
        return redirect()->route('wholesellers.index');
    }

    public function bulk_customer_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $customer_id) {
                $customer = User::findOrFail($customer_id);
                $customer->customer_products()->delete();
                $this->destroy($customer_id);
            }
        }
        return 1;
    }

    public function login($id)
    {
        $user = User::findOrFail(decrypt($id));
        auth()->login($user, true);
        return redirect()->route('dashboard');
    }

    public function ban($id)
    {
        $user = User::findOrFail(decrypt($id));
        if ($user->banned == 1) {
            $user->banned = 0;
            flash(translate('Wholeseller UnBanned Successfully'))->success();
        } else {
            $user->banned = 1;
            flash(translate('Wholeseller Banned Successfully'))->success();
        }
        $user->save();
        return back();
    }

    public function suspicious($id)
    {
        $user = User::findOrFail(decrypt($id));
        if ($user->is_suspicious == 1) {
            $user->is_suspicious = 0;
            flash(translate('Wholeseller unsuspected Successfully'))->success();
        } else {
            $user->is_suspicious = 1;
            flash(translate('Wholeseller suspected Successfully'))->success();
        }
        $user->save();
        return back();
    }
}