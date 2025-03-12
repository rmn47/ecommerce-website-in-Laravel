<?php

namespace App\Http\Controllers\Preorder\seller;

use App\Http\Controllers\Controller;
use App\Models\PreorderCommissionHistory;
use Illuminate\Http\Request;

class PreorderCommissionHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date_range = null;
        $commission_history = PreorderCommissionHistory::where('seller_id', auth()->user()->id)->orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }

        $commission_history = $commission_history->paginate(10);
        return view('preorder.seller.commission_history.index', compact('commission_history', 'date_range'));
    }
}
