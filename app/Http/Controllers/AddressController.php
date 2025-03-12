<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\City;
use App\Models\State;
use App\Models\Pincode;
use Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
            'postal_code' => 'required|string', // Pincode
            'phone' => 'required|string|max:12',
            'country_code' => 'required|string',
        ]);

        // Verify the selected pincode is active for the city
        $pincode_exists = Pincode::where('city_id', $request->city_id)
            ->where('pincode', $request->postal_code)
            ->where('is_active', 1)
            ->exists();
        if (!$pincode_exists) {
            flash(translate('The selected pincode is not active or not available for this city.'))->error();
            return back();
        }

        $address = new Address;
        if ($request->has('customer_id')) {
            $address->user_id = $request->customer_id;
        } else {
            $address->user_id = Auth::user()->id;
        }
        $address->address = $request->address;
        $address->country_id = $request->country_id;
        $address->state_id = $request->state_id;
        $address->city_id = $request->city_id;
        $address->longitude = $request->longitude;
        $address->latitude = $request->latitude;
        $address->postal_code = $request->postal_code;
        $address->phone = '+' . $request->country_code . $request->phone;
        $address->save();

        flash(translate('Address info stored successfully'))->success();
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
        $data['address_data'] = Address::findOrFail($id);
        $data['states'] = State::where('status', 1)->where('country_id', $data['address_data']->country_id)->get();
        $data['cities'] = City::where('status', 1)->where('state_id', $data['address_data']->state_id)->get();
        $data['pincodes'] = Pincode::where('city_id', $data['address_data']->city_id)->where('is_active', 1)->get();

        $returnHTML = view('frontend.partials.address.address_edit_modal', $data)->render();
        return response()->json(array('data' => $data, 'html' => $returnHTML));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
            'postal_code' => 'required|string', // Pincode
            'phone' => 'required|string|max:12',
        ]);

        // Verify the selected pincode is active for the city
        $pincode_exists = Pincode::where('city_id', $request->city_id)
            ->where('pincode', $request->postal_code)
            ->where('is_active', 1)
            ->exists();
        if (!$pincode_exists) {
            flash(translate('The selected pincode is not active or not available for this city.'))->error();
            return back();
        }

        $address = Address::findOrFail($id);
        $address->address = $request->address;
        $address->country_id = $request->country_id;
        $address->state_id = $request->state_id;
        $address->city_id = $request->city_id;
        $address->longitude = $request->longitude;
        $address->latitude = $request->latitude;
        $address->postal_code = $request->postal_code;
        $address->phone = $request->phone; // Assuming phone includes country code from form
        $address->save();

        flash(translate('Address info updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        if (!$address->set_default) {
            $address->delete();
            flash(translate('Address deleted successfully'))->success();
            return back();
        }
        flash(translate('Default address cannot be deleted'))->warning();
        return back();
    }

    /**
     * Get states for a given country via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStates(Request $request)
    {
        $states = State::where('status', 1)->where('country_id', $request->country_id)->get();
        $html = '<option value="">' . translate("Select State") . '</option>';

        foreach ($states as $state) {
            $html .= '<option value="' . $state->id . '">' . $state->name . '</option>';
        }

        return response()->json($html);
    }

    /**
     * Get cities for a given state via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCities(Request $request)
    {
        $cities = City::where('status', 1)->where('state_id', $request->state_id)->get();
        $html = '<option value="">' . translate("Select City") . '</option>';

        foreach ($cities as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->getTranslation('name') . '</option>';
        }

        return response()->json($html);
    }

    /**
     * Get pincodes for a given city via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPincodes(Request $request)
    {
        $pincodes = Pincode::where('city_id', $request->city_id)->where('is_active', 1)->get();
        $html = '<option value="">' . translate("Select Pincode") . '</option>';
    
        foreach ($pincodes as $pincode) {
            $html .= '<option value="' . $pincode->pincode . '">' . $pincode->pincode . '</option>';
        }
    
        return response()->json($html);
    }

    /**
     * Set an address as default.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function set_default($id)
    {
        foreach (Auth::user()->addresses as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $address = Address::findOrFail($id);
        $address->set_default = 1;
        $address->save();

        return back();
    }
}