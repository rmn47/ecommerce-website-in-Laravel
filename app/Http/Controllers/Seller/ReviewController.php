<?php

namespace App\Http\Controllers\Seller;

use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $sortSearch     =  $request->search != null ? $request->search : null; 
        $sortByRating   =  $request->rating != null ? $request->rating : null; 
        $sellerID       =  $request->seller_id != null ? $request->seller_id : 'all'; 

        $products = Product::join('reviews', 'reviews.product_id', '=', 'products.id')
                    ->where('products.user_id', auth()->user()->id)
                    ->groupBy('products.id');
        $products = $sortByRating != null ? $products->orderBy('products.rating', $sortByRating) : $products->orderBy('products.created_at', 'desc');
 
        if ($sortSearch != null) {
            $products->where(function ($q) use ($sortSearch){
                $q->where('products.name', 'like', '%'.$sortSearch.'%')
                ->orWhereHas('product_translations', function ($q) use ($sortSearch) {
                    $q->where('name', 'like', '%' . $sortSearch . '%');
                });
            });
        }        
        $products = $products->select("products.id","products.thumbnail_img", "products.name", "products.user_id",  "products.rating")->paginate(15);
        return view('seller.product_review.index', compact('products', 'sortSearch','sortByRating'));
    }

    public function detailReviews(Request $request, $productId){
        $product = Product::whereId($productId)->first();
        if (env('DEMO_MODE') != 'On') {
            $product->reviews()->update(['viewed' => 1]);
        }
        $reviews = $product->reviews()->paginate(15);
        return view('seller.product_review.review_details', compact('reviews', 'product'));
    }

}
