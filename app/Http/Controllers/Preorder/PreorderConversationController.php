<?php

namespace App\Http\Controllers\Preorder;
use App\Http\Controllers\Controller;
use App\Models\PreorderConversationMessage;
use App\Models\PreorderConversationThread;
use App\Models\PreorderProduct;
use Illuminate\Http\Request;

class PreorderConversationController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_preorder_product_conversations'])->only('adminIndex');
        $this->middleware(['permission:view_detail_preorder_product_conversation'])->only('adminShow');
        $this->middleware(['permission:delete_preorder_product_conversation'])->only('conversationDestroy');
    }
    

    // Admin Conversation List
    public function adminIndex()
    {
        if (get_setting('conversation_system') == 1) {
            $conversations = PreorderConversationThread::whereHas('sender')->whereHas('receiver')->orderBy('updated_at', 'desc')->paginate(10);
            return view('preorder.backend.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }
    }

    // Customer Conversation List
    public function customerIndex()
    {
        if (get_setting('conversation_system') == 1) {
            $conversations = PreorderConversationThread::where('sender_id', auth()->id())->whereHas('receiver')->orderBy('updated_at', 'desc')->paginate(8);
            return view('preorder.frontend.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }
    }

    public function adminShow($id)
    {
        $adminId = get_admin()->id;
        $conversation = PreorderConversationThread::findOrFail(decrypt($id));
        if($conversation->receiver_id == get_admin()->id){
            $conversation->messages()->where('sender_id', '!=' , $adminId)->whereReceiverViewed(0)->update(
                [
                    'receiver_viewed' => 1
                ]
            );
        }
        return view('preorder.backend.conversations.show', compact('conversation'));
    }

    public function customerShow($id)
    {
        $userId = auth()->user()->id;
        $conversation = PreorderConversationThread::findOrFail(decrypt($id));
        if($conversation->sender_id == $userId){
            $conversation->messages()->where('sender_id', '!=' , $userId)->whereReceiverViewed(0)->update(
                [
                    'receiver_viewed' => 1
                ]
            );
        }
        return view('preorder.frontend.conversations.show', compact('conversation'));
    }

    // Conversation Modal
    public function preorderConversationModal(Request $request)
    {
        $product = PreorderProduct::where('id', $request->product_id)->first();
        $conversation = PreorderConversationThread::where('sender_id', auth()->user()->id)->where('preorder_product_id', $product->id)->first();
        return view('preorder.common.models.product_conversation_modal', compact('product', 'conversation'));
    }

    // Conversation Store
    public function store(Request $request)
    {
        $authUserId = auth()->user()->id;
        $conversationThread = PreorderConversationThread::firstOrNew([
            'preorder_product_id' => $request->product_id,
            'sender_id' => $authUserId
        ]);
        if(!$conversationThread->exists){
            $product = PreorderProduct::where('id', $request->product_id)->first();
            $conversationThread->preorder_product_id = $request->product_id;
            $conversationThread->sender_id = $authUserId;
            $conversationThread->receiver_id = $product->user_id;
            $conversationThread->title = $request->title;
            $conversationThread->save();
        }
        else {
            // This is for update the updated_at time
            $conversationThread->touch();
        }

        if ($conversationThread->save()) {
            $conversationMessage = new PreorderConversationMessage();
            $conversationMessage->preorder_conversation_thread_id = $conversationThread->id;
            $conversationMessage->sender_id = $authUserId;
            $conversationMessage->message = $request->message;
            $conversationMessage->save();
        }

        flash(translate('Message has been sent to seller'))->success();
        return back();
    }

    public function messageReply(Request $request){
        $this->messageReplySrore($request->all());
        flash(translate('Message Sent Successfully'))->success();
        return back();
    }

    public function messageReplyCustomer(Request $request){
        $this->messageReplySrore($request->all());
        flash(translate('Message Sent Successfully'))->success();
        return back();
    }

    public function messageReplySrore($data){

        $userId = in_array(auth()->user()->user_type, ['admin', 'staff']) ? get_admin()->id : auth()->id();
        $message = new PreorderConversationMessage();
        $message->preorder_conversation_thread_id = $data['conversation_thread_id'];
        $message->sender_id = $userId;
        $message->message = $data['message'];
        $message->save();

        // This is for update the updated_at time
        $message->preorderConversationThread()->touch();;
    }   

    public function refresh(Request $request)
    {
        $userId = auth()->user()->id;
        $conversation = PreorderConversationThread::findOrFail(decrypt($request->id));
        if($conversation->sender_id == $userId){
            $conversation->messages()->where('sender_id', '!=' , $userId)->whereReceiverViewed(0)->update(
                [
                    'receiver_viewed' => 1
                ]
            );
        }
        return view('preorder.common.messages', compact('conversation'));
    }

    public function conversationDestroy($id){
        $conversations = PreorderConversationThread::findOrFail(decrypt($id));
        $conversations->messages()->delete();
        $conversations->delete();
        flash(translate('Product has been deleted successfully'))->success();
        return back();
    }
}
