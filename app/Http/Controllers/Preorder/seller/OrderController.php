<?php

namespace App\Http\Controllers\Preorder\seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Preorder;
use App\Services\PreorderService;
use Carbon\Carbon;
use Route;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $authUserId = auth()->user()->id;
        $orders         = Preorder::whereProductOwnerId($authUserId)->orderBy('id', 'desc');
        $data['date']   = $request->date;
        $data['sort_search'] = $request->search ?? null;
        $status = $request->order_status ?? 'all';
        $routeName      = Route::currentRouteName();

        // Not completed Prepayment Preorders
        if($routeName == 'seller.delayed_prepayment_preorders.list'){
            $orders->where('prepayment_confirm_status', 0)
                    ->where('request_preorder_status', 2)
                    ->whereHas('preorder_product', function ($query) {
                        $query->where('is_prepayment', 1);
                    })
                ->where('request_preorder_time', '<', Carbon::now()->subDay());
        }

        // Not completed Final Preorders
        elseif($routeName == 'seller.delayed_final_orders.list'){
            $orders->where(function ($query) {
                $query->whereHas('preorder_product', function ($subQuery) {
                    // Check if prepayment is enabled, check from Prepayment confirm status & Time
                    $subQuery->where('is_prepayment', 1);
                })
                ->where('prepayment_confirm_status', 2)
                ->where('final_order_status', 0)
                ->where('prepayment_confirmation_time', '<', Carbon::now()->subDay())
                ->orWhere(function ($subQuery) {
                    // When prepayment is disabled, check from Final Order confirm status & Time
                    $subQuery->whereHas('preorder_product', function ($innerQuery) {
                        $innerQuery->where('is_prepayment', 0);
                    })
                    ->where('request_preorder_status', 2)
                    ->where('final_order_status', 0)
                    ->where('request_preorder_time', '<', Carbon::now()->subDay());
                });
            });
        }

        // Filter By date
        if ($data['date'] != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $data['date'])[0])) . '  00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $data['date'])[1])) . '  23:59:59');
        }

        // Search by order Code
        if ($data['sort_search']) {
            $orders = $orders->where('order_code', 'like', '%' .  $data['sort_search'] . '%');
        }

        // Filter by Status 
        if($status != null){

            if($status == 'requested'){
                $orders->where('request_preorder_status', 1);
            }
            elseif($status == 'accepted_requests'){
                $orders->where('request_preorder_status', 2)->where('prepayment_confirm_status',0);
            }
            elseif($status == 'prepayment_requests'){
                $orders->where('prepayment_confirm_status',1);
            }
            elseif($status == 'confirmed_prepayments'){
                $orders->where('prepayment_confirm_status', 2)->where('final_order_status', 0);
            }
            elseif($status == 'final_preorders'){
                $orders->where('final_order_status', [1,2]);
            }
            elseif($status == 'in_shipping'){
                $orders->where('shipping_status',2)->where('delivery_status', 0);
            }
            elseif($status == 'delivered'){
                $orders->where('delivery_status', 2);
            }
            elseif($status == 'refund'){
                $orders->where('refund_status',2);
            }
        }
        $data['status'] = $status;
        
        $data['orders'] = $orders->paginate(15);

        $data['preorder_count'] = Preorder::whereProductOwnerId($authUserId)->count();
        $data['preorder_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('request_preorder_status',1)->count();
        $data['accepted_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('request_preorder_status',2)->where('prepayment_confirm_status', 0)->count();
        $data['prepayment_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('prepayment_confirm_status',1)->count();
        $data['confirmed_prepayment_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('prepayment_confirm_status',2)->where('final_order_status', 0)->count();
        $data['final_preorder_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('final_order_status', [1,2])->count();
        $data['preorder_request_in_shipping_count'] = Preorder::whereProductOwnerId($authUserId)->where('shipping_status',2)->where('delivery_status', 0)->count();
        $data['preorder_product_delivered_count'] = Preorder::whereProductOwnerId($authUserId)->where('delivery_status',2)->count();
        $data['preorder_product_refunded_count'] = Preorder::whereProductOwnerId($authUserId)->where('refund_status',2)->count();
        return view('preorder.seller.orders.index', $data);
    }


    public function show($id)
    {
        $order =  Preorder::with(['preorder_product','user','address'])->find(decrypt($id));
        $order->update(['is_viewed'=>1]);
        $sort_search = '';
        return view('preorder.seller.orders.show', compact('sort_search', 'order'));
    }

    public function orderStatusUpdate(Request $request, $id){

        (new PreorderService)->preorderStatusUpdate($request, $id);

        flash(translate('Data has been updated successfully'))->success();
        return redirect()->back();
    }


    public function bulkPreorderDelete(Request $request)
    {
        if ($request->order_ids) {
            foreach ($request->order_ids as $order_id) {
                Preorder::destroy($order_id);
            }
        }
        return 1;
    }

    public function destroy($id){
        Preorder::destroy($id);
        flash(translate('Data has been deleted successfully'))->success();
        return redirect()->back();
    }
}
