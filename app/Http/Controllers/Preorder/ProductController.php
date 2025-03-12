<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Preorder;
use App\Models\PreorderProduct;
use App\Models\PreorderProductQuery;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'ok';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data['categories'] = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        $data['brands'] = Brand::all();
        return view('preorder.common.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('preorder-product.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function product_details(string $slug)
    {
        $product = PreorderProduct::with(['preorder','user', 'category', 'brand', 'preorder_wholesale_prices', 'preorder_sample_order', 'preorder_prepayment', 'preorder_discount_periods', 'preorder_discount', 'preorder_shipping'])->where('product_slug', $slug)->first();
        $product = filter_single_preorder_product($product);

        if (! $product) {
            flash(translate('No product found!!'))->warning();
            return redirect()->back();
        }

        $data['product'] =  $product;
        $more_products = null;

        $more_product_ids = json_decode($product->more_products, true);
        if (is_array($more_product_ids) && !empty($more_product_ids)) {
            $more_products = PreorderProduct::whereIn('id', $more_product_ids)->get();
        } else {
            $more_products = collect(); // Empty collection if no valid IDs
        }
        $frequently_bought_product_ids = json_decode($product->frequently_bought_product, true);
        if (is_array($frequently_bought_product_ids) && !empty($frequently_bought_product_ids)) {
            $fq_bought_products = Product::whereIn('id',$frequently_bought_product_ids)->get();
        } else {
            $fq_bought_products = collect(); // Empty collection if no valid IDs
        }
        $data['more_products'] =  $more_products;
        $data['fq_bought_products'] =  $fq_bought_products;
        // Product Queries
        $preorderProductQuery = PreorderProductQuery::where('preorder_product_id', $product->id);
        if(auth()->check()){
            $preorderProductQuery->where('customer_id', '!=', auth()->user()->id);
        }
        $data['product_queries'] = $preorderProductQuery->latest('id')->paginate(1, ['*'], 'queryPage');
        $data['faqs'] = Faq::where('status',1)->get();

        // Product Reviews
        $data['review_status'] =  (auth()->check() && (Preorder::whereProductId($product->id)->where('user_id', auth()->user()->id)->whereDeliveryStatus(2)->count() > 0) ) ? 1 : 0;
        $data['reviews'] = $product->preorderProductreviews()->where('status', 1)->orderBy('created_at', 'desc')->paginate(1, ['*'], 'reviewPage');
        return view('preorder.frontend.product_details', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
