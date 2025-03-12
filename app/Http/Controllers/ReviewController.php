<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Auth;
use Route;

class ReviewController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_product_reviews'])->only('index');
        $this->middleware(['permission:publish_product_review'])->only('updatePublished');
        $this->middleware(['permission:add_custom_review'])->only('customReviewCreate');
        $this->middleware(['permission:edit_custom_review'])->only('customReviewEdit','customReviewUpdate');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sortSearch     =  $request->search != null ? $request->search : null; 
        $sortByRating   =  $request->rating != null ? $request->rating : null; 
        $sellerID       =  $request->seller_id != null ? $request->seller_id : 'all'; 

        $products = Product::join('reviews', 'reviews.product_id', '=', 'products.id')
                            ->groupBy('products.id');
        $products = $sortByRating != null ? $products->orderBy('products.rating', $sortByRating) : $products->orderBy('products.created_at', 'desc');

        if ($sellerID != 'all') {
            $products->where('products.user_id', $sellerID);
        }  
        if ($sortSearch != null) {
            $products->where(function ($q) use ($sortSearch){
                $q->where('products.name', 'like', '%'.$sortSearch.'%')
                ->orWhereHas('product_translations', function ($q) use ($sortSearch) {
                    $q->where('name', 'like', '%' . $sortSearch . '%');
                });
            });
        }        
        $products = $products->select("products.id","products.thumbnail_img", "products.name", "products.user_id",  "products.rating")->paginate(15);
        $sellers = User::whereUserType('seller')->where('email_verified_at','!=', null)->get();
        return view('backend.product.reviews.index', compact('products', 'sellers', 'sortSearch','sortByRating', 'sellerID'));
    }

    public function detailReviews(Request $request, $productId){
        $product = Product::whereId($productId)->first();
        if (env('DEMO_MODE') != 'On') {
            $product->reviews()->update(['viewed' => 1]);
        }
        $reviewType = $request->review_type == null ? 'real' :  $request->review_type;
        $reviews = $product->reviews()->whereType($reviewType)->paginate(15);
        $customerReviewCount = $reviewType == 'real' ? $reviews->count() : $product->reviews()->whereType('real')->count();
        $customReviewCount = $reviewType == 'custom' ? $reviews->count() : $product->reviews()->whereType('custom')->count();
        return view('backend.product.reviews.detail_reviews', compact('reviews', 'product','reviewType', 'customerReviewCount', 'customReviewCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function customReviewCreate($productId = null){
        if($productId == null ){
            $categories = Category::where('parent_id', 0)
                ->where('digital', 0)
                ->with('childrenCategories')
                ->get();
        }
        else
        {
            $categories = [];
        }
        $product = $productId != null ? Product::whereId($productId)->first() : null ;
        return view('backend.product.reviews.create_custom_review', compact('product', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $review             = new Review;
        $review->product_id = $request->product_id;
        if($authUser->user_type == 'customer'){
            $review->user_id = $authUser->id;
        }
        else {
            $review->type = 'custom';
            $review->custom_reviewer_name     = $request->custom_reviewer_name;
            $review->custom_reviewer_image    = $request->custom_reviewer_image;
        }
        $review->rating     = $request->rating;
        $review->comment    = $request->comment;
        $review->photos     = implode(',', $request->photos);
        $review->viewed     = '0';
        if(($request->review_date_type == "custom") && $request->custom_date != null){
            $review->created_at = $request->custom_date;
            $review->created_at_is_custom = 1;
        }
        $review->save();
        
        $product = Product::findOrFail($request->product_id);
        $reviewCount = Review::whereProductId($product->id)->whereStatus(1)->count();
        if ( $reviewCount > 0) {
            $product->rating = Review::whereProductId($product->id)->whereStatus(1)->sum('rating') /  $reviewCount;
        } else {
            $product->rating = 0;
        }
        $product->save();

        if ($product->added_by == 'seller') {
            $seller = $product->user->shop;
            $seller->rating = (($seller->rating * $seller->num_of_reviews) + $review->rating) / ($seller->num_of_reviews + 1);
            $seller->num_of_reviews += 1;
            $seller->save();
        }

        flash(translate('Review has been submitted successfully'))->success();
        if($authUser->user_type == 'customer'){
            return back();
        }
        else {
            return redirect()->route('detail-reviews', $product->id.'?review_type=custom');
        }
    }

    public function customReviewEdit($id){
        $review = Review::whereId($id)->first();
        return view('backend.product.reviews.edit_custom_review', compact('review'));
    }

    public function customReviewUpdate(Request $request){
        $review = Review::findOrFail($request->id);
        $review->custom_reviewer_name     = $request->custom_reviewer_name;
        $review->custom_reviewer_image    = $request->custom_reviewer_image;
        
        $review->rating     = $request->rating;
        $review->comment    = $request->comment;
        $review->photos     = implode(',', $request->photos);
        if(isset($request->custom_date) && $request->custom_date != null){
            $review->created_at = $request->custom_date;
            $review->created_at_is_custom = 1;
        }
        $review->save();
        
        $product = $review->product;
        $reviewCount = Review::whereProductId($product->id)->whereStatus(1)->count();
        if ($reviewCount > 0) {
            $product->rating = Review::whereProductId($product->id)->whereStatus(1)->sum('rating') / $reviewCount;
        } else {
            $product->rating = 0;
        }
        $product->save();

        flash(translate('Review has been updated successfully'))->success();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updatePublished(Request $request)
    {
        $review = Review::findOrFail($request->id);
        $review->status = $request->status;
        $review->save();

        $product = Product::findOrFail($review->product->id);
        if (Review::where('product_id', $product->id)->where('status', 1)->count() > 0) {
            $product->rating = Review::where('product_id', $product->id)->where('status', 1)->sum('rating') / Review::where('product_id', $product->id)->where('status', 1)->count();
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

    public function product_review_modal(Request $request)
    {
        $product = Product::where('id', $request->product_id)->first();
        $review = Review::where('user_id', Auth::user()->id)->where('product_id', $product->id)->first();
        return view('frontend.user.product_review_modal', compact('product', 'review'));
    }

    public function getProductByCategory(Request $request){
        $products = Product::whereCategoryId($request->category_id)->whereAddedBy('admin')->isApprovedPublished()->whereAuctionProduct(0)->orderBy('created_at', 'desc')->get();
        return view('backend.product.reviews.get_review_product_by_category', compact('products'));
    }

}
