<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Utility\EmailUtility;
use Hash;
use Storage;

class CustomerController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_customers'])->only('index');
        $this->middleware(['permission:add_customer'])->only('create');
        $this->middleware(['permission:login_as_customer'])->only('login');
        $this->middleware(['permission:ban_customer'])->only('ban');
        $this->middleware(['permission:mark_customer_suspected'])->only('suspicious');
        $this->middleware(['permission:delete_customer'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $verification_status =  $request->verification_status ?? null;
        $users = User::where('user_type', 'customer')
            ->where(function ($query) {
                $query->where('is_wholeseller', '!=', 1)
                    ->orWhereNull('is_wholeseller'); // Include NULL values as well
            })
            ->orderBy('created_at', 'desc');

        if($verification_status != null){
            $users = $verification_status == 'verified' ? $users->where('email_verified_at', '!=', null) : $users->where('email_verified_at', null);
        }
        if ($request->has('search')){
            $sort_search = $request->search;
            $users->where(function ($q) use ($sort_search){
                $q->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%');
            });
        }
        $users = $users->paginate(15);
        return view('backend.customer.customers.index', compact('users', 'sort_search', 'verification_status'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.customer.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'user_type' => 'required|in:customer,wholeseller',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255',
            'address' => 'required|string|max:500',
            'gst_no' => 'nullable|string|max:50',
            'drug_license_no' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_wholeseller' => 'sometimes|in:0,1',
        ]);
    
        if (User::where('email', $request->email)->first() != null) {
            flash(translate('Email already exists.'))->error();
            return back();
        }
    
        if (User::where('phone', '+'.$request->country_code.$request->phone)->first() != null) {
            flash(translate('Phone already exists.'))->error();
            return back();
        }
    
        $password = substr(hash('sha512', rand()), 0, 8);
        $avatarPath = null;
    
        if ($request->hasFile('photo')) {
            $avatarPath = $request->file('photo')->store('uploads/users', 'public');
        }
    
        $user = User::create([
            'user_type' => $request->user_type,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => '+'.$request->country_code.$request->phone,
            'address' => $request->address,
            'gst_no' => $request->gst_no,
            'drug_license_no' => $request->drug_license_no,
            'avatar_original' => $avatarPath, // Changed from 'photo' to 'avatar_original'
            'is_wholeseller' => $request->is_wholeseller ?? 0,
            'password' => Hash::make($password),
            'verification_code' => rand(100000, 999999),
        ]);
    
        // Email or OTP logic remains the same
        // if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
        //     try {
        //         EmailUtility::customer_registration_email('registration_from_system_email_to_customer', $user, $password);
        //         if (get_setting('email_verification') != 1) {
        //             $user->email_verified_at = now();
        //             $user->save();
        //         } else {
        //             EmailUtility::email_verification($user, 'customer');
        //         }
        //     } catch (\Exception $e) {
        //         $user->delete();
        //         flash(translate('Registration failed. Please try again later.'))->error();
        //         return back();
        //     }
        // } elseif ($request->phone && addon_is_activated('otp_system')) {
        //     $otpController = new OTPVerificationController;
        //     $otpController->account_opening($user, $password);
        // }
    
        flash(translate('Registration successful.'))->success();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $user = User::findOrFail(decrypt($id));
            $wholeseller = null; // Default to null for customers
            if ($user->user_type === 'wholeseller' || $user->is_wholeseller == 1) {
                $wholeseller = true; // Only set to true if explicitly a wholeseller
            }
            return view('backend.customer.customers.create', compact('user', 'wholeseller'));
        } catch (\Exception $e) {
            flash(translate('Invalid user ID or decryption failed.'))->error();
            return redirect()->route('customers.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
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
    
        if ($request->hasFile('photo')) {
            if ($user->avatar_original) { // Changed from 'photo' to 'avatar_original'
                Storage::disk('public')->delete($user->avatar_original);
            }
            $data['avatar_original'] = $request->file('photo')->store('uploads/users', 'public'); // Changed from 'photo' to 'avatar_original'
        }
    
        $user->update($data);
        flash(translate('User updated successfully'))->success();
        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        $customer->customer_products()->delete();

        User::destroy($id);
        flash(translate('Customer has been deleted successfully'))->success();
        return redirect()->route('customers.index');
    }

    public function bulk_customer_delete(Request $request) {
        if($request->id) {
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

    public function ban($id) {
        $user = User::findOrFail(decrypt($id));

        if($user->banned == 1) {
            $user->banned = 0;
            flash(translate('Customer UnBanned Successfully'))->success();
        } else {
            $user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $user->save();

        return back();
    }
    public function suspicious($id) {
        $user = User::findOrFail(decrypt($id));

        if($user->is_suspicious == 1) {
            $user->is_suspicious = 0;
            flash(translate('Customer unsuspected  Successfully'))->success();
        } else {
            $user->is_suspicious = 1;
            flash(translate('Customer suspected Successfully'))->success();
        }

        $user->save();

        return back();
    }
}
