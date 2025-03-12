<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\CityTranslation;
use App\Models\State;
use App\Models\Pincode;

class CityController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:manage_shipping_cities'])->only('index','create','destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_city = $request->sort_city;
        $sort_state = $request->sort_state;
        $sort_pincode = $request->sort_pincode;
        
        $cities_queries = City::query();
        if($request->sort_city) {
            $cities_queries->where('name', 'like', "%$sort_city%");
        }
        if($request->sort_state) {
            $cities_queries->where('state_id', $request->sort_state);
        }
        if($request->sort_pincode) {
            $cities_queries->whereHas('pincodes', function($query) use ($sort_pincode) {
                $query->where('pincode', 'like', "%$sort_pincode%");
            });
        }
        
        $cities = $cities_queries->with('pincodes')->orderBy('status', 'desc')->paginate(15);
        $states = State::where('status', 1)->get();

        return view('backend.setup_configurations.cities.index', compact('cities', 'states', 'sort_city', 'sort_state', 'sort_pincode'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $city = new City;
        $city->name = $request->name;
        $city->cost = $request->cost;
        $city->state_id = $request->state_id;
        $city->save();

        if($request->pincodes && is_array($request->pincodes)) {
            foreach($request->pincodes as $pincode) {
                if($pincode) {
                    Pincode::create([
                        'city_id' => $city->id,
                        'pincode' => $pincode,
                        'is_active' => 1
                    ]);
                }
            }
        }

        flash(translate('City has been inserted successfully'))->success();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $city = City::with('pincodes')->findOrFail($id);
        $states = State::where('status', 1)->get();
        return view('backend.setup_configurations.cities.edit', compact('city', 'lang', 'states'));
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
        $city = City::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $city->name = $request->name;
        }
        $city->state_id = $request->state_id;
        $city->cost = $request->cost;
        $city->save();

        // Update pincodes
        $city->pincodes()->delete();
        if($request->pincodes && is_array($request->pincodes)) {
            foreach($request->pincodes as $pincode) {
                if($pincode) {
                    Pincode::create([
                        'city_id' => $city->id,
                        'pincode' => $pincode,
                        'is_active' => 1
                    ]);
                }
            }
        }

        $city_translation = CityTranslation::firstOrNew(['lang' => $request->lang, 'city_id' => $city->id]);
        $city_translation->name = $request->name;
        $city_translation->save();

        flash(translate('City has been updated successfully'))->success();
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
        $city = City::findOrFail($id);
        $city->city_translations()->delete();
        City::destroy($id);

        flash(translate('City has been deleted successfully'))->success();
        return redirect()->route('cities.index');
    }

    public function updateStatus(Request $request){
        $city = City::findOrFail($request->id);
        $city->status = $request->status;
        $city->save();

        return 1;
    }
}
