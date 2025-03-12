<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use App\Models\PreorderProduct;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:preorder_dashboard'])->only('index');
    }

    public function index()
    {
        $data['total_preorder_roducts'] = PreorderProduct::count();
        $data['live_preorder_products'] = PreorderProduct::where('is_published',1)->count();
        $data['in_house_sales'] = Preorder::where('delivery_status',2)->where('refund_status','!=' ,2)->where('product_owner_id', get_admin()->id)->sum('grand_total');
        $data['seller_sales'] = Preorder::where('delivery_status',2)->where('refund_status','!=' ,2)->where('product_owner', '!=','admin')->sum('grand_total');
        $data['totalSalesThisMonth'] = Preorder::where('delivery_status', 2)->where('refund_status','!=' ,2)
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->sum('subtotal');
        $data['in_shipping_orders'] = Preorder::where('shipping_status',2)->where('delivery_status',[0,1])->count();
        $data['is_delivered_orders'] = Preorder::where('delivery_status',2)->count();

        $data['preorder_request_count'] = Preorder::where('request_preorder_status',1)->where('prepayment_confirm_status',[0,1])->count();
        $data['accepted_request_count'] = Preorder::where('request_preorder_status',2)->where('prepayment_confirm_status',[0,1])->count();
        $data['prepayment_request_count'] = Preorder::where('prepayment_confirm_status',1)->count();
        $data['confirmed_prepayment_request_count'] = Preorder::where('prepayment_confirm_status',2)->where('shipping_status',[0,1])->count();
        $data['final_preorder_request_count'] = Preorder::where('delivery_status',2)->where('refund_status', 0)->count();
        $delayed_prepayment_orders_count = Preorder::where('prepayment_confirm_status', 0)
                                                    ->where('request_preorder_status', 2)
                                                    ->where('final_order_status', 0)
                                                    ->whereHas('preorder_product', function ($query) {
                                                        $query->where('is_prepayment', 1);
                                                    })
                                                ->where('request_preorder_time', '<', Carbon::now()->subDay());
                                                                
        $delayed_final_orders_count = Preorder::where(function ($query) {
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
        // Last 12 month sales data
        $salesData = [];
        $currentYear = Carbon::now()->year;
        
        for ($month = 1; $month <= 12; $month++) {
            $salesData[] = Preorder::where('delivery_status', 2)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $currentYear)
                ->sum('subtotal');
        }
        // Pass this $salesData array to your JavaScript
        $data['monthlySales'] = $salesData;

        // most 5 selling products
        $data['top_selling_products'] = Preorder::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
        ->where('delivery_status', 2)
        ->groupBy('product_id')
        ->orderByDesc('total_quantity')
        ->take(5)
        ->with('preorder_product') // Assuming a relation in Preorder model to PreorderProduct
        ->get();

        return view('preorder.backend.dashboard.index', $data);
    }


    public function preorderByProductsSection(Request $request)
    {
        $adminId = get_admin()->id;
        $preorderProducts = Preorder::join('preorder_products', 'preorders.product_id', '=', 'preorder_products.id');
        if($request->user_type == 'inhouse'){
            $preorderProducts->where('preorder_products.user_id', $adminId);
        }
        elseif($request->user_type == 'seller'){
            $preorderProducts->where('preorder_products.user_id','!=', $adminId);
        }
        $preorderProducts = $preorderProducts->select('preorder_products.id', DB::raw('SUM(preorders.quantity) as total_quantity'))
            ->groupBy('preorders.product_id')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        return view('preorder.backend.dashboard.preorder_by_products_section', compact('preorderProducts'))->render();
    }
}
