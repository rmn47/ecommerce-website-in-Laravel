<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use Cache;
use Cookie;
use App\Models\Page;
use App\Models\Shop;
use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\OrderDetail;
use App\Models\ProductQuery;
use Illuminate\Http\Request;
use App\Models\AffiliateConfig;
use App\Models\CustomerPackage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Cart;
use App\Models\Preorder;
use App\Models\PreorderProduct;
use App\Utility\EmailUtility;
use Artisan;
use DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use ZipArchive;
use Carbon\Carbon;
use App\Helpers\ProductHelper;

class HomeController extends Controller
{
    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lang = get_system_language() ? get_system_language()->code : null;
        $featured_categories = Cache::rememberForever('featured_categories', function () {
            return Category::with('bannerImage')->where('featured', 1)->get();
        });

        return view('frontend.' . get_setting('homepage_select') . '.index', compact('featured_categories', 'lang'));
    }

    public function load_todays_deal_section()
    {
        $todays_deal_products = filter_products(Product::where('todays_deal', '1'))->orderBy('id', 'desc')->get();
        return view('frontend.' . get_setting('homepage_select') . '.partials.todays_deal', compact('todays_deal_products'));
    }

    public function load_newest_product_section()
    {
        $newest_products = Cache::remember('newest_products', 3600, function () {
            return filter_products(Product::latest())->limit(12)->get();
        });

        return view('frontend.' . get_setting('homepage_select') . '.partials.newest_products_section', compact('newest_products'));
    }

    public function load_featured_section()
    {
        return view('frontend.' . get_setting('homepage_select') . '.partials.featured_products_section');
    }

    public function load_best_selling_section()
    {
        return view('frontend.' . get_setting('homepage_select') . '.partials.best_selling_section');
    }

    public function load_auction_products_section()
    {
        if (!addon_is_activated('auction')) {
            return;
        }
        $lang = get_system_language() ? get_system_language()->code : null;
        return view('auction.frontend.' . get_setting('homepage_select') . '.auction_products_section', compact('lang'));
    }

    public function load_home_categories_section()
    {
        return view('frontend.' . get_setting('homepage_select') . '.partials.home_categories_section');
    }

    public function load_best_sellers_section()
    {
        return view('frontend.' . get_setting('homepage_select') . '.partials.best_sellers_section');
    }
    public function load_preorder_featured_products_section()
    {

        // $preorder_products = Cache::remember('preorder_products', 3600, function () {
            $preorder_products = PreorderProduct::where('is_published', 1)->where('is_featured',1)
            ->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('user_type', 'admin');
                })->orWhereHas('user.shop', function ($q) {
                    $q->where('verification_status', 1);
                });
            })
            ->latest()
            ->limit(12)
            ->get();
        // });
        return view('frontend.' . get_setting('homepage_select') . '.partials.preorder_products_section', compact('preorder_products'));
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        if (Route::currentRouteName() == 'seller.login' && get_setting('vendor_system_activation') == 1) {
            return view('auth.' . get_setting('authentication_layout_select') . '.seller_login');
        } else if (Route::currentRouteName() == 'deliveryboy.login' && addon_is_activated('delivery_boy')) {
            return view('auth.' . get_setting('authentication_layout_select') . '.deliveryboy_login');
        }
        return view('auth.' . get_setting('authentication_layout_select') . '.user_login');
    }

    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        if ($request->has('referral_code') && addon_is_activated('affiliate_system')) {
            try {
                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }

                Cookie::queue('referral_code', $request->referral_code, $cookie_minute);
                $referred_by_user = User::where('referral_code', $request->referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            } catch (\Exception $e) {
            }
        }
        return view('auth.' . get_setting('authentication_layout_select') . '.user_registration');
    }

    public function cart_login(Request $request)
    {
        $user = null;
        if ($request->get('phone') != null) {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('phone', "+{$request['country_code']}{$request['phone']}")->first();
        } elseif ($request->get('email') != null) {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->first();
        }

        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($request->has('remember')) {
                    auth()->login($user, true);
                } else {
                    auth()->login($user, false);
                }
            } else {
                flash(translate('Invalid email or password!'))->warning();
            }
        } else {
            flash(translate('Invalid email or password!'))->warning();
        }
        return back();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the customer/seller dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if (Auth::user()->user_type == 'seller') {
            return redirect()->route('seller.dashboard');
        } elseif (Auth::user()->user_type == 'customer') {
            $users_cart = Cart::where('user_id', auth()->user()->id)->first();
            if ($users_cart) {
                flash(translate('You had placed your items in the shopping cart. Try to order before the product quantity runs out.'))->warning();
            }
            return view('frontend.user.customer.dashboard');
        } elseif (Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.dashboard');
        } else {
            abort(404);
        }
    }

    public function profile(Request $request)
    {
        if (Auth::user()->user_type == 'seller') {
            return redirect()->route('seller.profile.index');
        } elseif (Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.profile');
        } else {
            return view('frontend.user.profile');
        }
    }

    public function userProfileUpdate(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:organization,seller,customer,wholeseller',
            'first_name' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:15',
            'email_id' => 'required|string|email|max:255',
            'billing_address' => 'required|string|max:500',
            'gst_no' => 'nullable|string|max:50',
            'drug_license_no' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_wholeseller' => 'sometimes|in:0,1',
            'pan_card' => 'nullable|string|max:10|required_if:user_type,seller',
        ]);
    
        try {
            DB::beginTransaction();
    
            $user = User::where('phone', $request->mobile_no)->first();
            if (!$user) {
                $user = new User();
                $user->phone = $request->mobile_no;
            }
    
            if ($user->email !== $request->email_id && User::where('email', $request->email_id)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => translate('The email address is already taken.'),
                ], 422);
            }
    
            $user->name = $request->first_name;
            $user->email = $request->email_id;
            $user->address = $request->billing_address;
    
            $userType = $request->user_type;
            $isWholeseller = $request->input('is_wholeseller', 0);
    
            if ($userType === 'wholeseller' || $isWholeseller == 1) {
                $user->user_type = 'customer';
                $user->is_wholeseller = 1;
            } else {
                $user->user_type = $userType;
                $user->is_wholeseller = 0;
            }
    
            if (in_array($user->user_type, ['organization', 'seller'])) {
                $user->gst_no = $request->gst_no;
                $user->drug_license_no = $request->drug_license_no;
            }
    
            if ($user->user_type === 'seller') {
                $user->pan_card = $request->pan_card;
    
                $shop = Shop::where('user_id', $user->id)->first();
                if (!$shop) {
                    $shop = new Shop();
                    $shop->user_id = $user->id;
                    $shop->name = $request->first_name . "'s Shop";
                    $shop->address = $request->billing_address;
                    $shop->slug = 'shop-' . $user->id;
                    $shop->verification_status = 0;
                    $shop->save();
                }
            } else {
                $user->pan_card = null;
            }
    
            if ($request->hasFile('photo')) {
                $user->avatar_original = $request->file('photo')->store('uploads/users', 'public');
            }
    
            $user->save();
    
            DB::commit();
    
            if (!Auth::check()) {
                Auth::login($user);
            }
    
            // Send Email Notification
            try {
                $emailData = [
                    'email' => $user->email,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'user_type' => $user->user_type,
                    'is_wholeseller' => $user->is_wholeseller ? 'Yes' : 'No',
                    'gst_no' => $user->gst_no ?? 'N/A',
                    'drug_license_no' => $user->drug_license_no ?? 'N/A',
                    'pan_card' => $user->pan_card ?? 'N/A',
                    'updated_at' => $user->updated_at->toDateTimeString(),
                ];
    
                \Log::info('Attempting to send email to: ' . $user->email);
    
                EmailUtility::send_email_with_template(
                    $user->email,
                    translate('Profile Update - ') . config('app.name'),
                    'emails.guest_account_opening',
                    $emailData
                );
    
                \Log::info('Profile update email queued successfully to: ' . $user->email);
            } catch (\Exception $emailException) {
                \Log::error('Failed to send profile update email: ' . $emailException->getMessage() . ' | Stack Trace: ' . $emailException->getTraceAsString());
            }
    
            // Send SMS Notification
            try {
                $serverUrl = env('MSGCLUB_SERVER_URL');
                $authKey = env('MSGCLUB_AUTH_KEY');
                $senderId = env('MSGCLUB_SENDER_ID');
                $routeId = env('MSGCLUB_ROUTE_ID');
                $smsContentType = env('MSGCLUB_LANGUAGE');
                $msg = "Dear {$user->name}, your profile has been successfully updated on " . config('app.name') . ".";
    
                $postData = [
                    'mobileNumbers' => $user->phone,
                    'smsContent' => $msg,
                    'senderId' => $senderId,
                    'routeId' => $routeId,
                    'smsContentType' => $smsContentType
                ];
    
                $data_json = json_encode($postData);
                $url = "http://" . $serverUrl . "/rest/services/sendSMS/sendGroupSms?AUTH_KEY=" . $authKey;
    
                \Log::info('Attempting to send SMS to: ' . $user->phone . ' with payload: ' . $data_json);
    
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_json)
                    ],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $data_json,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0
                ]);
    
                $output = curl_exec($ch);
    
                if (curl_errno($ch)) {
                    \Log::error('SMS sending failed: ' . curl_error($ch));
                } else {
                    $obj = json_decode($output);
                    if (isset($obj->responseCode) && $obj->responseCode == '3001') {
                        \Log::info('SMS sent successfully to: ' . $user->phone);
                    } else {
                        \Log::error('SMS API response error: ' . $output);
                    }
                }
                curl_close($ch);
            } catch (\Exception $smsException) {
                \Log::error('Failed to send SMS notification: ' . $smsException->getMessage() . ' | Stack Trace: ' . $smsException->getTraceAsString());
            }
    
            $redirectUrl = $user->user_type === 'seller' ? route('dashboard') : route('home');
    
            return response()->json([
                'status' => 'success',
                'message' => translate('Profile updated successfully!'),
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'is_wholeseller' => $user->is_wholeseller,
                    'pan_card' => $user->pan_card
                ]
            ]);
    
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Profile update failed: ' . $e->getMessage() . ' | Stack Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => translate('An error occurred while updating the profile.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function flash_deal_details($slug)
    {
        $today = strtotime(date('Y-m-d H:i:s'));
        $flash_deal = FlashDeal::where('slug', $slug)
            ->where('start_date', "<=", $today)
            ->where('end_date', ">", $today)
            ->first();
        if ($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));
        else {
            abort(404);
        }
    }

    public function trackOrder(Request $request)
    {
        if ($request->has('order_code')) {
            $order = Order::where('code', $request->order_code)->first();
            if ($order != null) {
                return view('frontend.track_order', compact('order'));
            }
        }
        return view('frontend.track_order');
    }

    public function product(Request $request, $slug)
    {
        if (!Auth::check()) {
            session(['link' => url()->current()]);
        }
    
        $detailedProduct = Product::with('reviews', 'brand', 'stocks', 'user', 'user.shop')
            ->where('auction_product', 0)
            ->where('slug', $slug)
            ->where('approved', 1)
            ->first();
    
        if ($detailedProduct != null && $detailedProduct->published) {
            if ((get_setting('vendor_system_activation') != 1) && $detailedProduct->added_by == 'seller') {
                abort(404);
            }
    
            if ($detailedProduct->added_by == 'seller' && $detailedProduct->user->banned == 1) {
                abort(404);
            }
    
            if (!addon_is_activated('wholesale') && $detailedProduct->wholesale_product == 1) {
                abort(404);
            }
    
            $product_queries = ProductQuery::where('product_id', $detailedProduct->id)
                ->where('customer_id', '!=', Auth::id())
                ->latest('id')
                ->paginate(3);
            $total_query = ProductQuery::where('product_id', $detailedProduct->id)->count();
            $reviews = $detailedProduct->reviews()->where('status', 1)->orderBy('created_at', 'desc')->paginate(3);
    
            // Fetch all top-level categories (reusing logic from all_categories())
            $categories = Category::with('childrenCategories')->where('parent_id', 0)->orderBy('order_level', 'desc')->get();
    
            // Find the product's category based on its category_id
            $category = null;
            foreach ($categories as $cat) {
                if ($cat->id == $detailedProduct->category_id) {
                    $category = $cat;
                    break;
                }
                // Check children categories
                foreach ($cat->childrenCategories as $child) {
                    if ($child->id == $detailedProduct->category_id) {
                        $category = $child;
                        break 2; // Break out of both loops
                    }
                }
            }
    
            // Pagination using Ajax
            if (request()->ajax()) {
                if ($request->type == 'query') {
                    return Response::json(View::make('frontend.partials.product_query_pagination', array('product_queries' => $product_queries))->render());
                }
                if ($request->type == 'review') {
                    return Response::json(View::make('frontend.product_details.reviews', array('reviews' => $reviews))->render());
                }
            }
    
            // Review status
            $review_status = 0;
            if (Auth::check()) {
                $OrderDetail = OrderDetail::with(['order' => function ($q) {
                    $q->where('user_id', Auth::id());
                }])->where('product_id', $detailedProduct->id)->where('delivery_status', 'delivered')->first();
                $review_status = $OrderDetail ? 1 : 0;
            }
    
            if ($request->has('product_referral_code') && addon_is_activated('affiliate_system')) {
                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }
                Cookie::queue('product_referral_code', $request->product_referral_code, $cookie_minute);
                Cookie::queue('referred_product_id', $detailedProduct->id, $cookie_minute);
    
                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();
    
                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            }
    
            if (get_setting('last_viewed_product_activation') == 1 && Auth::check() && auth()->user()->user_type == 'customer') {
                lastViewedProducts($detailedProduct->id, auth()->user()->id);
            }
            
            $substituteProducts = ProductHelper::getSubstituteProducts($detailedProduct);
    
            // Pass the category and categories to the view
            return view('frontend.product_details', compact('detailedProduct', 'product_queries', 'total_query', 'reviews', 'review_status', 'category', 'categories', 'substituteProducts'));
        }
        abort(404);
    }
    
    public function showAllSubstitutes($slug)
    {
        $detailedProduct = Product::where('slug', $slug)->where('approved', 1)->where('published', 1)->firstOrFail();
        $substituteProducts = ProductHelper::getSubstituteProducts($detailedProduct, 20); // Show more products
    
        return view('frontend.substitute_products', compact('detailedProduct', 'substituteProducts'));
    }
    
    public static function getSubstituteProducts($currentProduct, $limit = 5)
    {
        $currentSalts = self::extractSaltComposition($currentProduct->description);
        
        if (empty($currentSalts)) {
            return collect(); // Return empty collection if no salts found
        }
    
        $substituteProducts = Product::with('brand')
            ->where('id', '!=', $currentProduct->id)
            ->where('published', 1)
            ->where('approved', 1)
            ->where('auction_product', 0)
            ->get()
            ->filter(function ($product) use ($currentSalts) {
                $productSalts = self::extractSaltComposition($product->description);
                
                if (empty($productSalts)) {
                    return false;
                }
    
                $matchingSalts = array_intersect_assoc($currentSalts, $productSalts);
                return count($matchingSalts) === count($currentSalts) && 
                       count($matchingSalts) === count($productSalts);
            })
            ->map(function ($product) use ($currentProduct) {
                // Add price comparison and manufacturer data
                $product->price_comparison = self::calculatePriceComparison($product, $currentProduct);
                $product->manufacturer = $product->brand ? $product->brand->name : self::extractManufacturer($product->description) ?? 'Unknown Manufacturer';
                return $product;
            })
            ->take($limit);
    
        return $substituteProducts;
    }
    
    public static function extractManufacturer($description)
    {
        if (preg_match('/MANUFACTURER.*?<p.*?>(.*?)<\/p>/is', $description, $match)) {
            return trim(strip_tags($match[1]));
        }
        return null;
    }

    public function shop($slug)
    {
        if (get_setting('vendor_system_activation') != 1) {
            return redirect()->route('home');
        }
        $shop  = Shop::where('slug', $slug)->first();
        if ($shop != null) {
            if ($shop->user->banned == 1) {
                abort(404);
            }
            if ($shop->verification_status != 0) {
                return view('frontend.seller_shop', compact('shop'));
            } else {
                return view('frontend.seller_shop_without_verification', compact('shop'));
            }
        }
        abort(404);
    }

    public function filter_shop(Request $request, $slug, $type)
    {
        if (get_setting('vendor_system_activation') != 1) {
            return redirect()->route('home');
        }
        $shop  = Shop::where('slug', $slug)->first();
        if ($shop != null && $type != null) {
            if ($shop->user->banned == 1) {
                abort(404);
            }
            if ($type == 'all-products') {
                $sort_by = $request->sort_by;
                $min_price = $request->min_price;
                $max_price = $request->max_price;
                $selected_categories = array();
                $brand_id = null;
                $rating = null;

                $conditions = ['user_id' => $shop->user->id, 'published' => 1, 'approved' => 1];

                if ($request->brand != null) {
                    $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
                    $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
                }

                $products = Product::where($conditions);

                if ($request->has('selected_categories')) {
                    $selected_categories = $request->selected_categories;
                    $products->whereIn('category_id', $selected_categories);
                }

                if ($min_price != null && $max_price != null) {
                    $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
                }

                if ($request->has('rating')) {
                    $rating = $request->rating;
                    $products->where('rating', '>=', $rating);
                }

                switch ($sort_by) {
                    case 'newest':
                        $products->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $products->orderBy('created_at', 'asc');
                        break;
                    case 'price-asc':
                        $products->orderBy('unit_price', 'asc');
                        break;
                    case 'price-desc':
                        $products->orderBy('unit_price', 'desc');
                        break;
                    default:
                        $products->orderBy('id', 'desc');
                        break;
                }

                $products = $products->paginate(24)->appends(request()->query());

                return view('frontend.seller_shop', compact('shop', 'type', 'products', 'selected_categories', 'min_price', 'max_price', 'brand_id', 'sort_by', 'rating'));
            }

            if ($type == 'all-preorder-products') {
                $sort_by = $request->sort_by;
                $min_price = $request->min_price;
                $max_price = $request->max_price;
                $selected_categories = array();
                $is_available = array();
                $brand_id = null;
                $rating = null;

                $conditions = ['user_id' => $shop->user->id, 'is_published' => 1];

                if ($request->brand != null) {
                    $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
                    $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
                }

                $products = PreorderProduct::where('is_published',1)->where('user_id' , $shop->user->id);

                if ($request->has('is_available') && $request->is_available !== null) {
                    $availability = $request->is_available;
                    $currentDate = Carbon::now()->format('Y-m-d');
                    $products->where(function ($query) use ($availability, $currentDate) {
                        if ($availability == 1) {
                            $query->where('is_available', 1)->orWhere('available_date', '<=', $currentDate);
                        } else {
                            $query->where(function ($query) {
                                $query->where('is_available', '!=', 1)
                                      ->orWhereNull('is_available');
                            })
                            ->where(function ($query) use ($currentDate) {
                                $query->whereNull('available_date')
                                      ->orWhere('available_date', '>', $currentDate);
                            });
                        }
                    });
                
                    $is_available = $availability;
                } else {
                    $is_available = null;

                }


                if ($request->has('selected_categories')) {
                    $selected_categories = $request->selected_categories;
                    $products->whereIn('category_id', $selected_categories);
                }

                if ($min_price != null && $max_price != null) {
                    $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
                }

                if ($request->has('rating')) {
                    $rating = $request->rating;
                    $products->where('rating', '>=', $rating);
                }

                switch ($sort_by) {
                    case 'newest':
                        $products->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $products->orderBy('created_at', 'asc');
                        break;
                    case 'price-asc':
                        $products->orderBy('unit_price', 'asc');
                        break;
                    case 'price-desc':
                        $products->orderBy('unit_price', 'desc');
                        break;
                    default:
                        $products->orderBy('id', 'desc');
                        break;
                }

                $products = $products->paginate(24)->appends(request()->query());

                return view('frontend.seller_shop', compact('shop', 'type', 'products', 'selected_categories', 'min_price', 'max_price', 'brand_id', 'sort_by', 'rating','is_available'));
            }

            return view('frontend.seller_shop', compact('shop', 'type'));
        }
        abort(404);
    }

    public function all_categories(Request $request)
    {
        $categories = Category::with('childrenCategories')->where('parent_id', 0)->orderBy('order_level', 'desc')->get();

        // dd($categories);
        return view('frontend.all_category', compact('categories'));
    }

    public function all_brands(Request $request)
    {
        $brands = Brand::all();
        return view('frontend.all_brand', compact('brands'));
    }

    public function home_settings(Request $request)
    {
        return view('home_settings.index');
    }

    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if (is_array($request->top_categories) && in_array($category->id, $request->top_categories)) {
                $category->top = 1;
                $category->save();
            } else {
                $category->top = 0;
                $category->save();
            }
        }

        foreach (Brand::all() as $key => $brand) {
            if (is_array($request->top_brands) && in_array($brand->id, $request->top_brands)) {
                $brand->top = 1;
                $brand->save();
            } else {
                $brand->top = 0;
                $brand->save();
            }
        }

        flash(translate('Top 10 categories and brands have been updated successfully'))->success();
        return redirect()->route('home_settings.index');
    }

    public function variant_price(Request $request)
    {
        $product = Product::find($request->id);
        $str = '';
        $quantity = 0;
        $tax = 0;
        $max_limit = 0;
    
        // Build the variant string
        if ($request->has('color')) {
            $str = $request['color'];
        }
    
        if (json_decode($product->choice_options) != null) {
            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }
    
        // Get the specific stock for the variant
        $product_stock = $product->stocks->where('variant', $str)->first();
    
        // Use variant-specific price, discount, and discount type if available
        $price = $product_stock ? $product_stock->price : $product->unit_price;
        $discount = $product_stock ? $product_stock->discount : $product->discount;
        $discount_type = $product_stock ? $product_stock->discount_type : $product->discount_type;
    
        // Handle wholesale pricing if applicable
        if ($product->wholesale_product && $product_stock) {
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)
                ->where('max_qty', '>=', $request->quantity)
                ->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }
    
        // Stock quantity and limits
        $quantity = $product_stock ? $product_stock->qty : $product->stocks->sum('qty');
        $max_limit = $product_stock ? $product_stock->qty : $product->stocks->sum('qty');
    
        // Stock availability check
        $in_stock = ($quantity >= 1 && $product->min_qty <= $quantity) ? 1 : 0;
    
        // Product Stock Visibility
        if ($product->stock_visibility_state == 'text') {
            $quantity = ($quantity >= 1 && $product->min_qty <= $quantity) ? translate('In Stock') : translate('Out Of Stock');
        }
    
        // Discount calculation (variant-specific or product-level)
        $discount_applicable = false;
        if ($product->discount_start_date == null || 
            (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date && 
             strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date)) {
            $discount_applicable = true;
        }
    
        $original_price = $price; // Store original price before discount
        if ($discount_applicable && $discount > 0) {
            if ($discount_type == 'percent') {
                $price -= ($price * $discount) / 100;
            } elseif ($discount_type == 'amount') {
                $price -= $discount;
            }
        }
    
        // Tax calculation
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
    
        $price += $tax;
    
        // Return response with all necessary data
        return array(
            'price' => $original_price, // Original price before discount
            'discounted_price' => single_price($price * $request->quantity), // Price after discount and tax
            'discount' => $discount,
            'discount_type' => $discount_type,
            'quantity' => $quantity,
            'digital' => $product->digital,
            'variation' => $str,
            'max_limit' => $max_limit,
            'in_stock' => $in_stock
        );
    }

    public function sellerpolicy()
    {
        $page =  Page::where('type', 'seller_policy_page')->first();
        return view("frontend.policies.sellerpolicy", compact('page'));
    }

    public function returnpolicy()
    {
        $page =  Page::where('type', 'return_policy_page')->first();
        return view("frontend.policies.returnpolicy", compact('page'));
    }

    public function supportpolicy()
    {
        $page =  Page::where('type', 'support_policy_page')->first();
        return view("frontend.policies.supportpolicy", compact('page'));
    }

    public function terms()
    {
        $page =  Page::where('type', 'terms_conditions_page')->first();
        return view("frontend.policies.terms", compact('page'));
    }

    public function privacypolicy()
    {
        $page =  Page::where('type', 'privacy_policy_page')->first();
        return view("frontend.policies.privacypolicy", compact('page'));
    }


    public function get_category_items(Request $request)
    {
        $categories = Category::with('childrenCategories')->findOrFail($request->id);
        return view('frontend.partials.category_elements', compact('categories'));
    }

    public function premium_package_index()
    {
        $customer_packages = CustomerPackage::all();
        return view('frontend.user.customer_packages_lists', compact('customer_packages'));
    }


    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        if (isUnique($email) == '0') {
            $response['status'] = 2;
            $response['message'] = translate('Email already exists!');
            return json_encode($response);
        }

        $response = $this->send_email_change_verification_mail($request, $email);
        return json_encode($response);
    }


    // Form request
    public function update_email(Request $request)
    {
        $email = $request->email;
        if (isUnique($email)) {
            $this->send_email_change_verification_mail($request, $email);
            flash(translate('A verification mail has been sent to the mail you provided us with.'))->success();
            return back();
        }

        flash(translate('Email already exists!'))->warning();
        return back();
    }

    public function send_email_change_verification_mail($request, $email)
    {
        $user = auth()->user();
        $response['status'] = 0;
        $response['message'] = 'Unknown';
        try {
            EmailUtility::email_verification($user, $user->user_type);
            $response['status'] = 1;
            $response['message'] = translate("Your verification mail has been Sent to your email.");
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param =  $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                if ($user->user_type == 'seller') {
                    return redirect()->route('seller.dashboard');
                }
                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();
        return redirect()->route('dashboard');
    }

    public function reset_password_with_code(Request $request)
    {
        if (($user = User::where('email', $request->email)->where('verification_code', $request->code)->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                flash(translate('Password updated successfully'))->success();

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            } else {
                flash(translate("Password and confirm password didn't match"))->warning();
                return view('auth.' . get_setting('authentication_layout_select') . '.reset_password');
            }
        } else {
            flash(translate("Verification code mismatch"))->error();
            return view('auth.' . get_setting('authentication_layout_select') . '.reset_password');
        }
    }


    public function all_flash_deals()
    {
        $today = strtotime(date('Y-m-d H:i:s'));

        $data['all_flash_deals'] = FlashDeal::where('status', 1)
            ->where('start_date', "<=", $today)
            ->where('end_date', ">", $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return view("frontend.flash_deal.all_flash_deal_list", $data);
    }

    public function todays_deal()
    {
        $todays_deal_products = Cache::rememberForever('todays_deal_products', function () {
            return filter_products(Product::with('thumbnail')->where('todays_deal', '1'))->get();
        });

        return view("frontend.todays_deal", compact('todays_deal_products'));
    }

    public function all_seller(Request $request)
    {
        if (get_setting('vendor_system_activation') != 1) {
            return redirect()->route('home');
        }
        $shops = Shop::whereIn('user_id', verified_sellers_id())
            ->paginate(15);

        return view('frontend.shop_listing', compact('shops'));
    }

    public function all_coupons(Request $request)
    {
        $coupons = Coupon::where('status', 1)->where(function ($query) {
            $query->where('type', 'welcome_base')->orWhere(function ($query) {
                $query->where('type', '!=', 'welcome_base')->where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')));
            });
        })->paginate(15);

        return view('frontend.coupons', compact('coupons'));
    }

    public function inhouse_products(Request $request)
    {
        $products = filter_products(Product::where('added_by', 'admin'))->with('taxes')->paginate(12)->appends(request()->query());
        return view('frontend.inhouse_products', compact('products'));
    }

    public function import_data(Request $request)
    {
        $upload_path = $request->file('uploaded_file')->store('uploads', 'local');
        $sql_path = $request->file('sql_file')->store('uploads', 'local');

        $zip = new ZipArchive;
        $zip->open(base_path('public/'.$upload_path));
        $zip->extractTo('public/uploads/all');

        $zip1 = new ZipArchive;
        $zip1->open(base_path('public/'.$sql_path));
        $zip1->extractTo('public/uploads');

        Artisan::call('cache:clear');
        $sql_path = base_path('public/uploads/demo_data.sql');
        DB::unprepared(file_get_contents($sql_path));
    }
}
