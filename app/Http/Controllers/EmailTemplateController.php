<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:manage_email_templates'])->only('index', 'edit', 'update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $emailReceiver)
    {
        $addons = Addon::where('activated', 1)->pluck('unique_identifier')->toArray();
        $email_template_sort_search = (isset($request->email_template_sort_search) && $request->email_template_sort_search) ? $request->email_template_sort_search : null;
        $emailTemplates = EmailTemplate::where('receiver', $emailReceiver);

        // If email templated for addons, check addons are insatalled and activated.
        $emailTemplates->where(function ($query) use ($addons) {
                $query->whereAddon(null)
                    ->orWhere(function ($query) use ($addons) {
                        $query->whereIn('addon', $addons);
                    });
        });

        if ($email_template_sort_search != null){
            $notificationTypes = $emailTemplates->where('email_type', 'like', '%' . $email_template_sort_search . '%');
        }
        $emailTemplates = $emailTemplates->paginate(10);
        return view('backend.setup_configurations.email_templates.index', compact('emailTemplates', 'email_template_sort_search', 'emailReceiver'));
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
        //
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
        $emailTemplate  = EmailTemplate::findOrFail($id);
        return view('backend.setup_configurations.email_templates.edit', compact('emailTemplate'));
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
        $emailTemplate = EmailTemplate::findOrFail($id);
        $emailTemplate->subject = $request->subject;
        $emailTemplate->default_text = $request->default_text;
        $emailTemplate->save();

        flash(translate('Email Template has been updated successfully'))->success();
        return back();
    }

    public function updateStatus(Request $request) {
        $emailTemplate = EmailTemplate::findOrFail($request->id);
        $emailTemplate->status = $request->status;
        $emailTemplate->save();
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
        //
    }
}
