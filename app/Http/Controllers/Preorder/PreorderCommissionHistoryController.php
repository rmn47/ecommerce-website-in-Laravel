<?php

namespace App\Http\Controllers\Preorder;
use App\Http\Controllers\Controller;
use App\Models\PreorderCommissionHistory;
use App\Models\User;
use Illuminate\Http\Request;

class PreorderCommissionHistoryController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_preorder_seller_commission_history'])->only('index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $seller_id = null;
        $date_range = null;
        if ($request->seller_id) {
            $seller_id = $request->seller_id;
        }

        $commission_history = PreorderCommissionHistory::orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($seller_id) {
            $commission_history = $commission_history->where('seller_id', '=', $seller_id);
        }

        $commission_history = $commission_history->paginate(10);
        $sellers = User::where('user_type', '=', 'seller')->get();
        return view('preorder.backend.commission_history.index', compact('commission_history', 'seller_id', 'date_range', 'sellers'));
    }
}
