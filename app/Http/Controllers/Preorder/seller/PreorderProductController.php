<?php

namespace App\Http\Controllers\Preorder\seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\PreorderProduct;
use App\Models\Tax;
use App\Services\PreorderService;
use Artisan;
use Illuminate\Http\Request;

class PreorderProductController extends Controller
{

    protected $preorderService;

    public function __construct(PreorderService $preorderService)
    {
        $this->preorderService = $preorderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = auth()->user()->id;
        $products = PreorderProduct::with(['category', 'preorder_sample_order', 'preorder_prepayment'])->where('user_id', auth()->user()->id);
        $col_name = null;
        $query = null;
        $sort_search = null;
        
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('product_name', 'like', '%' . $sort_search . '%')
                ->orWhereHas('preorder_product_translations', function ($q) use ($sort_search) {
                    $q->where('product_name', 'like', '%' . $sort_search . '%');
                });
        }
        $products = $products->orderBy('created_at', 'desc')->paginate(10);

  
        $data['allProducts'] = PreorderProduct::where('user_id', $userId)->count();
        $data['publishedProductCount'] = PreorderProduct::where('user_id', $userId)->where('is_published', 1)->count();
        $data['unpublishedProductCount'] = PreorderProduct::where('user_id', $userId)->where('is_published', '!=', 1)->count();
        $data['discountedProductCount'] = PreorderProduct::where('user_id', $userId)->where('discount', 1)->count();

        return view('preorder.seller.products.index', $data, compact('products','col_name', 'query', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (addon_is_activated('seller_subscription')) {
            if (!seller_package_validity_check_for_preorder_product()) {
                flash(translate('Please upgrade your package.'))->warning();
                return back();
            }
        }

        
        $data['categories'] = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        $data['brands'] = Brand::all();
        $data['taxes'] = Tax::where('tax_status', 1)->get();
        return view('preorder.seller.products.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        if (addon_is_activated('seller_subscription')) {
            if (!seller_package_validity_check_for_preorder_product()) {
                flash(translate('Please upgrade your package.'))->warning();
                return redirect()->route('seller.products');
            }
        }


        // Validation
        $this->validate($request, [
            'product_name' => ['required'],
            'category_id' => ['required'],
            'unit' => ['required'],
            'unit_price' => ['required'],
            'coupon_amount' => $request->coupon_amount ? 'sometimes|numeric|lt:unit_price' : 'nullable',
            'discount' => $request->discount ? 'sometimes|numeric|lt:unit_price' : 'nullable',
        ]);

        // Product Store
        $this->preorderService->productStore($request);

        flash(translate('Preorder Product Info Stored Successfully'))->success();
        return redirect()->route('seller.preorder-product.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $preorderProduct = PreorderProduct::findOrFail($id);
        $data['categories'] = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        $data['product'] = $preorderProduct;
        $data['brands'] = Brand::all();
        $data['prepayment'] = $preorderProduct->preorder_prepayment;
        $data['sample_order'] = $preorderProduct->preorder_sample_order;
        $data['coupon'] = $preorderProduct->preorder_coupon;
        $data['refund'] = $preorderProduct->preorder_refund;
        $data['cod'] = $preorderProduct->preorder_cod;
        $data['discount'] = $preorderProduct->preorder_discount;
        $data['shipping'] = $preorderProduct->preorder_shipping;
        $data['stock'] = $preorderProduct->preorder_stock;
        $data['taxes'] = $preorderProduct->preorder_product_taxes;
        $data['lang'] = $request->lang;
        return view('preorder.seller.products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PreorderProduct $preorderProduct)
    {
        $this->validate($request, [
            'product_name' => ['required'],
            'category_id' => ['required'],
            'unit' => ['required'],
            'unit_price' => ['required'],
            'coupon_amount' => $request->coupon_amount ? 'sometimes|numeric|lt:unit_price' : 'nullable',
            'discount' => $request->discount ? 'sometimes|numeric|lt:unit_price' : 'nullable',
        ]);

        // Product Update
        $this->preorderService->productUpdate($request, $preorderProduct);

        flash(translate('Preorder product has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        (new PreorderService)->productdestroy($id);

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        flash(translate('Preorder Product has been deleted successfully'))->success();
        return back();
    }

    public function bulkProductDestroy(Request $request)
    {
        if ($request->product_ids) {
            foreach ($request->product_ids  as $product_id) {
                (new PreorderService)->productdestroy($product_id);
            }
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
        }
        return 1;
    }


    public function preorder_product_published(Request $request)
    {
        if (addon_is_activated('seller_subscription')) {
            if (!seller_package_validity_check_for_preorder_product()) {
                return 2;
            }
        }

        $product = PreorderProduct::findOrFail($request->id);
        $product->is_published = $request->status;
        $product->save();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        return 1;
    }

    public function preorder_product_featured(Request $request)
    {
        $product = PreorderProduct::findOrFail($request->id);
        $product->is_featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

}
