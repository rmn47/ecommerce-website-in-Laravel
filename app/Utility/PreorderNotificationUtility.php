<?php

namespace App\Utility;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PreorderNotification;

class PreorderNotificationUtility
{
    public static function preorderNotification($preorder, $statusType)
    {     
        $adminId = get_admin()->id;
        $userIds = array($preorder->user->id, $preorder->product_owner_id);
        if ($preorder->product_owner_id != $adminId) {
            array_push($userIds, $adminId);
        }
        $users = User::findMany($userIds);
        
        $order_notification = array();
        $order_notification['preorder_id'] = $preorder->id;
        $order_notification['order_code'] = $preorder->order_code;

        foreach($users as $user){
            $userType = in_array($user->user_type, ['admin','staff']) ? 'admin' : $user->user_type;
            $notificationType = get_notification_type('preorder_'.$statusType.'_'.$userType, 'type');
            if($notificationType != null && $notificationType->status == 1){
                $order_notification['notification_type_id'] = $notificationType->id;
                Notification::send($user, new PreorderNotification($order_notification));
            }
        }
    }
}
