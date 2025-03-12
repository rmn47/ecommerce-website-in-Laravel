<?php

namespace App\Http\Controllers\Preorder\seller;
use App\Http\Controllers\Controller;
use App\Models\PreorderConversationMessage;
use App\Models\PreorderConversationThread;
use Illuminate\Http\Request;

class PreorderConversationController extends Controller
{
    public function index()
    {
        if (get_setting('conversation_system') == 1) {
            $conversations = PreorderConversationThread::where('receiver_id', auth()->id())->whereHas('sender')->orderBy('updated_at', 'desc')->paginate(10);
            return view('preorder.seller.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }
    }

    public function show($id)
    {
        $userId = auth()->user()->id;
        $conversation = PreorderConversationThread::findOrFail(decrypt($id));
        if($conversation->receiver_id == $userId){
            $conversation->messages()->where('sender_id', '!=' , $userId)->whereReceiverViewed(0)->update(
                [
                    'receiver_viewed' => 1
                ]
            );
        }
        return view('preorder.seller.conversations.show', compact('conversation'));
    }

    public function messageReply(Request $request){
        $userId = in_array(auth()->user()->user_type, ['admin', 'staff']) ? get_admin()->id : auth()->id();
        $message = new PreorderConversationMessage();
        $message->preorder_conversation_thread_id = $request->conversation_thread_id;
        $message->sender_id = $userId;
        $message->message = $request->message;
        $message->save();

        // This is for update the updated_at time
        $message->preorderConversationThread()->touch();
        flash(translate('Message Sent Successfully'))->success();
        return back();
    }   
}
