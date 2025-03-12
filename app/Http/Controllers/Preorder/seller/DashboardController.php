<?php

namespace App\Http\Controllers\Preorder\seller;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use App\Models\PreorderProduct;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $authUserId = auth()->user()->id;
        $data['total_preorder_roducts']     = PreorderProduct::whereUserId($authUserId)->count();
        $data['live_preorder_products']     = PreorderProduct::whereUserId($authUserId)->where('is_published',1)->count();
        $delayed_prepayment_orders_count    = Preorder::whereProductOwnerId($authUserId)->where('prepayment_confirm_status', 0)
                                                ->where('request_preorder_status', 2)
                                                ->whereHas('preorder_product', function ($query) {
                                                    $query->where('is_available', 1)->where('is_prepayment', 1);
                                                    })
                                                ->where('request_preorder_time', '<', Carbon::now()->subDay());
                                                                
        $delayed_final_orders_count = Preorder::whereProductOwnerId($authUserId)->whereHas('preorder_product', function ($query) {
                                            $query->where('is_available', 1);
                                        })
                                        ->where(function ($query) {
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
                    
        
        $data['delayed_prepayment_orders_count'] = $delayed_prepayment_orders_count->count();
        $data['delayed_final_orders_count']      = $delayed_final_orders_count->count();

        $data['total_sales'] = Preorder::where('delivery_status',2)->where('refund_status','!=' ,2)->whereProductOwnerId($authUserId)->sum('subtotal');
        $data['totalSalesThisMonth'] = Preorder::whereProductOwnerId($authUserId)
                                        ->where('delivery_status', 2)
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->sum('subtotal');
        $data['in_shipping_orders'] = Preorder::whereProductOwnerId($authUserId)->where('shipping_status',2)->where('delivery_status',[0,1])->count();
        $data['is_delivered_orders'] = Preorder::whereProductOwnerId($authUserId)->where('delivery_status',2)->count();

        $data['preorder_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('request_preorder_status',1)->where('prepayment_confirm_status',[0,1])->count();
        $data['accepted_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('request_preorder_status',2)->where('prepayment_confirm_status',[0,1])->count();
        $data['prepayment_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('prepayment_confirm_status',1)->where('prepayment_confirm_status',[2,3])->count();
        $data['confirmed_prepayment_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('prepayment_confirm_status',2)->where('shipping_status',[0,1])->count();
        $data['final_preorder_request_count'] = Preorder::whereProductOwnerId($authUserId)->where('delivery_status',2)->count();
        
        // Last 12 month sales data
        $salesData = [];
        $currentYear = Carbon::now()->year;
        
        for ($month = 1; $month <= 12; $month++) {
            $salesData[] = Preorder::whereProductOwnerId($authUserId)
                            ->where('delivery_status', 2)
                            ->whereMonth('created_at', $month)
                            ->whereYear('created_at', $currentYear)
                            ->sum('subtotal');
        }
        // Pass this $salesData array to your JavaScript
        $data['monthlySales'] = $salesData;

        // most 5 selling products
        $data['preorderProducts'] = Preorder::join('preorder_products', 'preorders.product_id', '=', 'preorder_products.id')
                                    ->select('preorder_products.id', DB::raw('SUM(preorders.quantity) as total_quantity'))
                                    ->groupBy('preorders.product_id')
                                    ->where('preorders.product_owner_id', $authUserId)
                                    ->orderByDesc('total_quantity')
                                    ->take(5)
                                    ->get();

        return view('preorder.seller.dashboard.index', $data);
    }
}
