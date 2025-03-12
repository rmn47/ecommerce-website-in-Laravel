<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\WarrantyTranslation;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_product_warranties'])->only('index');
        $this->middleware(['permission:edit_product_warranty'])->only('edit');
        $this->middleware(['permission:delete_product_warranty'])->only('destroy');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $warranties = Warranty::orderBy('created_at', 'asc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $warranties->where('text', 'like', '%'.$sort_search.'%');
        }
        $warranties = $warranties->paginate(15);
        return view('backend.product.warranties.index', compact('warranties', 'sort_search'));
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
        $warranty = new Warranty();
        $warranty->text = $request->warranty_text;
        $warranty->logo = $request->logo;
        $warranty->save();

        $warranty_translation = WarrantyTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'warranty_id' => $warranty->id]);
        $warranty_translation->text = $request->warranty_text;
        $warranty_translation->save();

        flash(translate('New Warranty has been added successfully'))->success();
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
    public function edit(Request $request, $id)
    {
        $lang   = $request->lang;
        $warranty  = Warranty::findOrFail($id);
        return view('backend.product.warranties.edit', compact('warranty','lang'));
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
        $warranty = Warranty::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $warranty->text = $request->warranty_text;
        }
        $warranty->logo = $request->logo;
        $warranty->save();

        $warranty_translation = WarrantyTranslation::firstOrNew(['lang' => $request->lang, 'warranty_id' => $warranty->id]);
        $warranty_translation->text = $request->warranty_text;
        $warranty_translation->save();

        flash(translate('Warranty has been updated successfully'))->success();
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
        $warranty = Warranty::findOrFail($id);
        $warranty->warranty_translations()->delete();
        Warranty::destroy($id);

        flash(translate('Warranty has been deleted successfully'))->success();
        return back();
    }
}
