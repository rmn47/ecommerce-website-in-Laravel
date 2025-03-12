<?php

namespace App\Http\Controllers\Seller;

use App\Http\Requests\SellerProfileRequest;
use App\Models\User;
use Auth;
use Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $addresses = $user->addresses; 
        return view('seller.profile.index', compact('user', 'addresses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\SellerProfileRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SellerProfileRequest $request, $id)
    {
        if(env('DEMO_MODE') == 'On'){
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = User::findOrFail($id);
        
        // Update basic info
        $user->name = $request->name;
        $user->phone = $request->phone;
        
        // Update new fields
        $user->gst_no = $request->gst_no;
        $user->drug_license_no = $request->drug_license_no;
        $user->pan_card = $request->pan_card;

        // Handle password update
        if($request->new_password != null && ($request->new_password == $request->confirm_password)){
            $user->password = Hash::make($request->new_password);
        }
        
        // Handle photo update
        if($request->hasFile('photo')){
            $user->avatar_original = $request->file('photo')->store('uploads/users');
        } elseif($request->photo) {
            $user->avatar_original = $request->photo;
        }

        // Update shop details
        $shop = $user->shop;
        if($shop){
            $shop->cash_on_delivery_status = $request->cash_on_delivery_status;
            $shop->bank_payment_status = $request->bank_payment_status;
            $shop->bank_name = $request->bank_name;
            $shop->bank_acc_name = $request->bank_acc_name;
            $shop->bank_acc_no = $request->bank_acc_no;
            $shop->bank_routing_no = $request->bank_routing_no;
            $shop->save();
        }

        $user->save();

        flash(translate('Your Profile has been updated successfully!'))->success();
        return back();
    }
}