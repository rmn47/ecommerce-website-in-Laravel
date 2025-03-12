<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationTypeRequest;
use App\Models\NotificationType;
use App\Models\NotificationTypeTranslation;
use Illuminate\Http\Request;

class NotificationTypeController extends Controller
{

    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_preorder_notification_types'])->only('index');
        $this->middleware(['permission:edit_preorder_notification_type'])->only('edit');
        $this->middleware(['permission:update_preorder_notification_status'])->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $notification_type_sort_search = (isset($request->notification_type_sort_search) && $request->notification_type_sort_search) ? $request->notification_type_sort_search : null;
        $notificationUserType = $request->notification_user_type == null ? 'customer' :  $request->notification_user_type;

        $notificationTypes = NotificationType::where('user_type', $notificationUserType)->where('addon', 'preorder');

        if ($notification_type_sort_search != null){
            $notificationTypes = $notificationTypes->where('name', 'like', '%' . $notification_type_sort_search . '%')
                ->orWhereHas('notificationTypeTranslations', function ($q) use ($notification_type_sort_search) {
                    $q->where('name', 'like', '%' . $notification_type_sort_search . '%');
                });
        }
        $notificationTypes = $notificationTypes->orderByRaw("FIELD(type , 'custom') ASC")->paginate(10);
        return view('preorder.backend.notification_types.index', compact('notificationTypes', 'notification_type_sort_search', 'notificationUserType'));

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
        $notificationType  = NotificationType::findOrFail($id);
        return view('preorder.backend.notification_types.edit', compact('notificationType','lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NotificationTypeRequest $request, $id)
    {
        $notificationType = NotificationType::findOrFail($id);
        $notificationType->image = $request->image;
        $default_text = str_replace( array( '\'', '"', ',', ';','{', '}','\r', '\n' ), '', $request->default_text);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $notificationType->name = $request->name;
            $notificationType->default_text = $default_text;
        }
        $notificationType->save();

        $notificationTypeTranslation = NotificationTypeTranslation::firstOrNew(['lang' => $request->lang, 'notification_type_id' => $notificationType->id]);
        $notificationTypeTranslation->name = $request->name;
        $notificationTypeTranslation->default_text = $default_text;
        $notificationTypeTranslation->save();

        flash(translate('Notification Type has been updated successfully'))->success();
        return back();
    }
}
