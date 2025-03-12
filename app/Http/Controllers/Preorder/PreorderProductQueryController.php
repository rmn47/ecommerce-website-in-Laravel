<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\PreorderProduct;
use App\Models\PreorderProductQuery;
use Illuminate\Http\Request;

class PreorderProductQueryController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_preorder_product_queries'])->only('index');
        $this->middleware(['permission:reply_preorder_product_queries'])->only('reply');
    }

    /**
     * Retrieve queries that belongs to current seller
     */
    public function index()
    {
        $admin_id = get_admin()->id;
        $queries = PreorderProductQuery::where('seller_id', $admin_id)->latest()->paginate(20);
        return view('preorder.backend.product_query.index', compact('queries'));
    }

    /**
     * Retrieve specific query using query id.
     */
    public function show($id)
    {
        $query = PreorderProductQuery::find(decrypt($id));
        return view('preorder.backend.product_query.show', compact('query'));
    }

    /**
     * store products queries through the ProductQuery model
     * data comes from product details page
     * authenticated user can leave queries about the product
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|string',
        ]);
        $product = PreorderProduct::find($request->product);

        $query = new PreorderProductQuery();
        $query->customer_id = auth()->user()->id;
        $query->seller_id = $product->user_id;
        $query->preorder_product_id = $product->id;
        $query->question = $request->question;
        $query->save();
        flash(translate('Your query has been submittes successfully'))->success();
        return redirect()->back();
    }

    /**
     * Store reply against the question from Admin panel
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
        return redirect()->route('preorder.product_query.index');
    }
}
