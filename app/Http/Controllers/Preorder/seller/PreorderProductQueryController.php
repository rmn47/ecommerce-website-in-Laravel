<?php

namespace App\Http\Controllers\Preorder\seller;

use App\Http\Controllers\Controller;
use App\Models\PreorderProductQuery;
use Illuminate\Http\Request;

class PreorderProductQueryController extends Controller
{
    /**
     * Retrieve queries that belongs to current seller
     */
    public function index()
    {
        $queries = PreorderProductQuery::where('seller_id', auth()->user()->id)->latest()->paginate(20);
        return view('preorder.seller.product_query.index', compact('queries'));
    }
    /**
     * Retrieve specific query using query id.
     */
    public function show($id)
    {
        $query = PreorderProductQuery::find(decrypt($id));
        return view('preorder.seller.product_query.show', compact('query'));
    }
    /**
     * Store reply against the question from seller panel
     */

    public function reply(Request $request, $id)
    {
        $this->validate($request, [
            'reply' => 'required',
        ]);
        $query = PreorderProductQuery::find($id);
        $query->reply = $request->reply;
        $query->save();
        flash(translate('Replied successfully!'))->success();
        return redirect()->route('seller.preorder_product_query.index');
    }
}
