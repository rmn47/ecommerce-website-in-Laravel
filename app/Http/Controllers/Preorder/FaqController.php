<?php

namespace App\Http\Controllers\Preorder;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqTranslation;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_faqs'])->only('index');
        $this->middleware(['permission:add_faq'])->only('create');
        $this->middleware(['permission:edit_faq'])->only('edit');
        $this->middleware(['permission:update_faq_status'])->only('updateStatus');
        $this->middleware(['permission:delete_faq'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $faqs = Faq::orderBy('created_at', 'desc');
        
        if ($request->has('search')){
            $sort_search = $request->search;
            $faqs = $faqs->where('question', 'like', '%' . $sort_search . '%')
                ->orWhereHas('faq_translations', function ($q) use ($sort_search) {
                    $q->where('question', 'like', '%' . $sort_search . '%');
                });
        }
        $faqs = $faqs->paginate(15);
        return view('preorder.backend.faqs.index', compact('faqs', 'sort_search'));
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
        $faq = new Faq;
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        $faq_translation = FaqTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'faq_id' => $faq->id]);
        $faq_translation->question = $request->question;
        $faq_translation->answer = $request->answer;
        $faq_translation->save();

        flash(translate('FAQ has been inserted successfully'))->success();
        return redirect()->route('faqs.index');

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
        $faq  = Faq::findOrFail($id);
        return view('preorder.backend.faqs.edit', compact('faq','lang'));
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
        $faq = Faq::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            $faq->save();
        }

        $faq_translation = FaqTranslation::firstOrNew(['lang' => $request->lang, 'faq_id' => $faq->id]);
        $faq_translation->question = $request->question;
        $faq_translation->answer = $request->answer;
        $faq_translation->save();

        flash(translate('FAQ has been updated successfully'))->success();
        return back();

    }

    public function updateStatus(Request $request)
    {
        $faq = Faq::findOrFail($request->id);
        $faq->status = $request->status;
        $faq->save();
        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->faq_translations()->delete();
        $faq->delete();

        flash(translate('FAQ has been deleted successfully'))->success();
        return redirect()->route('faqs.index');

    }
}
