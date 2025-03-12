<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\PreorderProduct;
use App\Models\PreorderProductReview;
use App\Models\User;
use Illuminate\Http\Request;

class PreorderProductReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_all_preorder_product_queries'])->only('adminIndex');
        $this->middleware(['permission:update_preorder_product_review_status'])->only('updateStatus');
    }

    public function adminIndex(Request $request)
    {
        $sortSearch     =  $request->search != null ? $request->search : null; 
        $sortByRating   =  $request->rating != null ? $request->rating : null; 
        $sellerID       =  $request->seller_id != null ? $request->seller_id : 'all'; 

        $products = PreorderProduct::join('preorder_product_reviews', 'preorder_product_reviews.preorder_product_id', '=', 'preorder_products.id')
                            ->groupBy('preorder_products.id');
        $products = $sortByRating != null ? $products->orderBy('preorder_products.rating', $sortByRating) : $products->orderBy('preorder_products.created_at', 'desc');

        if ($sellerID != 'all') {
            $products->where('preorder_products.user_id', $sellerID);
        }  
        if ($sortSearch != null) {
            $products->where(function ($q) use ($sortSearch){
                $q->where('preorder_products.name', 'like', '%'.$sortSearch.'%')
                ->orWhereHas('product_translations', function ($q) use ($sortSearch) {
                    $q->where('name', 'like', '%' . $sortSearch . '%');
                });
            });
        }        
        $products = $products->select("preorder_products.id","preorder_products.thumbnail", "preorder_products.product_name", "preorder_products.user_id",  "preorder_products.rating")->paginate(15);
        $sellers = User::whereUserType('seller')->where('email_verified_at','!=', null)->get();
        return view('preorder.backend.product_reviews.index', compact('products', 'sellers', 'sortSearch','sortByRating', 'sellerID'));
    }

    public function detailReviews($productId){
        $product = PreorderProduct::whereId($productId)->first();
        if (env('DEMO_MODE') != 'On') {
            $product->preorderProductreviews()->update(['viewed' => 1]);
        }
        $reviews = $product->preorderProductreviews()->paginate(15);
        return view('preorder.backend.product_reviews.detail_reviews', compact('reviews', 'product'));
    }
    
    // Review Store
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $review   = new PreorderProductReview();
        $review->preorder_product_id = $request->product_id;
        $review->user_id    = $authUser->id;
        $review->rating     = $request->rating;
        $review->comment    = $request->comment;
        $review->photos     = implode(',', $request->photos);
        $review->viewed     = '0';
        $review->save();
        
        $product = PreorderProduct::findOrFail($request->product_id);
        $reviewCount = PreorderProductReview::wherePreorderProductId($product->id)->whereStatus(1)->count();
        if ( $reviewCount > 0) {
            $product->rating = PreorderProductReview::wherePreorderProductId($product->id)->whereStatus(1)->sum('rating') /  $reviewCount;
        } else {
            $product->rating = 0;
        }
        $product->save();

        if ($product->user->user_type == 'seller') {
            $seller = $product->user->shop;
            $seller->rating = (($seller->rating * $seller->num_of_reviews) + $review->rating) / ($seller->num_of_reviews + 1);
            $seller->num_of_reviews += 1;
            $seller->save();
        }

        flash(translate('Review has been submitted successfully'))->success();
        return back();
    }

    public function product_review_modal(Request $request)
    {
        $product = PreorderProduct::where('id', $request->product_id)->first();
        $review = PreorderProductReview::where('user_id', auth()->user()->id)->where('preorder_product_id', $product->id)->first();
        return view('preorder.common.models.product_review_modal', compact('product', 'review'));
    }

    // Review Status Update
    public function updateStatus(Request $request)
    {
        $review = PreorderProductReview::findOrFail($request->id);
        $review->status = $request->status;
        $review->save();

        $product = PreorderProduct::findOrFail($review->preorderProduct->id);
        if (PreorderProductReview::where('preorder_product_id', $product->id)->where('status', 1)->count() > 0) {
            $product->rating = PreorderProductReview::where('preorder_product_id', $product->id)->where('status', 1)->sum('rating') / PreorderProductReview::where('preorder_product_id', $product->id)->where('status', 1)->count();
        } else {
            $product->rating = 0;
        }
        $product->save();

        if ($product->added_by == 'seller') {
            $seller = $product->user->shop;
            if ($review->status) {
                $seller->rating = (($seller->rating * $seller->num_of_reviews) + $review->rating) / ($seller->num_of_reviews + 1);
                $seller->num_of_reviews += 1;
            } else {
                $seller->rating = (($seller->rating * $seller->num_of_reviews) - $review->rating) / max(1, $seller->num_of_reviews - 1);
                $seller->num_of_reviews -= 1;
            }

            $seller->save();
        }

        return 1;
    }

}
