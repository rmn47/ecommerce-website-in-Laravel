<?php

namespace App\Http\Controllers\Preorder\seller;

use App\Http\Controllers\Controller;
use App\Models\PreorderProduct;
use Illuminate\Http\Request;

class PreorderProductReviewController extends Controller
{
    public function index(Request $request)
    {
        $sortSearch     =  $request->search != null ? $request->search : null; 
        $sortByRating   =  $request->rating != null ? $request->rating : null; 
        $sellerID       =  $request->seller_id != null ? $request->seller_id : 'all'; 

        $products = PreorderProduct::join('preorder_product_reviews', 'preorder_product_reviews.preorder_product_id', '=', 'preorder_products.id')
                    ->where('preorder_products.user_id', auth()->user()->id)
                    ->groupBy('preorder_products.id');

        // Sort By Rating
        $products = $sortByRating != null ? $products->orderBy('preorder_products.rating', $sortByRating) : $products->orderBy('preorder_products.created_at', 'desc');
 
        // Search By Product Name
        if ($sortSearch != null) {
            $products->where(function ($q) use ($sortSearch){
                $q->where('preorder_products.product_name', 'like', '%'.$sortSearch.'%')
                ->orWhereHas('product_translations', function ($q) use ($sortSearch) {
                    $q->where('product_name', 'like', '%' . $sortSearch . '%');
                });
            });
        }        
        $products = $products->select("preorder_products.id","preorder_products.thumbnail", "preorder_products.product_name", "preorder_products.user_id",  "preorder_products.rating")->paginate(15);
        return view('preorder.seller.product_review.index', compact('products', 'sortSearch','sortByRating'));
    }

    public function detailReviews(Request $request, $productId){
        $product = PreorderProduct::whereId($productId)->first();
        if (env('DEMO_MODE') != 'On') {
            $product->preorderProductreviews()->update(['viewed' => 1]);
        }
        $preorder_product_reviews = $product->preorderProductreviews()->paginate(15);
        return view('preorder.seller.product_review.review_details', compact('preorder_product_reviews', 'product'));
    }

}
