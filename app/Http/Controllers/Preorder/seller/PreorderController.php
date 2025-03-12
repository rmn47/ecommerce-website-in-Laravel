<?php

namespace App\Http\Controllers\Preorder\seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PreorderController extends Controller
{
    // PreOrder Settings
    public function preorderSettings(){
        return view('preorder.seller.settings.index');
    } 

    public function updatePreorderInstruction(Request $request){
        $shop = auth()->user()->shop;
        $shop->preorder_request_instruction = $request->preorder_request_instruction;
        $shop->image_for_payment_qrcode = $request->image_for_payment_qrcode;
        $shop->pre_payment_instruction = $request->pre_payment_instruction;
        $shop->save();

        flash(translate('Setting Updated Successfully'))->success();
        return back();
    }
}
