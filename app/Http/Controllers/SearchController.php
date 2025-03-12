<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Search;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Shop;
use App\Models\Attribute;
use App\Models\AttributeCategory;
use App\Models\PreorderProduct;
use App\Utility\CategoryUtility;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function index(Request $request, $category_id = null, $brand_id = null)
    {
        // dd($request->all());
        $query = $request->keyword;
        $sort_by = $request->sort_by;
        $product_type = $request->product_type ?? 'general_product';
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;
        $attributes = Attribute::all();
        $selected_attribute_values = array();
        $colors = Color::all();
        $is_available = array();
        $selected_color = null;
        $category = [];
        $categories = [];

        $conditions = [];

        if(addon_is_activated('preorder') && $request->product_type == 'preorder_product'){
                $products = PreorderProduct::where('is_published',1);
                $products = filter_preorder_product($products);
            if ($category_id != null) {
                $category_ids[] = $category_id;
                $category = Category::with('childrenCategories')->find($category_id);

                $products = $category->preorderProducts();
            } else {
                $categories = Category::with('childrenCategories', 'coverImage')->where('level', 0)->orderBy('order_level', 'desc')->get();
            }

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

            if ($min_price != null && $max_price != null) {
                $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
            }

            if ($query != null) {

                $products->where(function ($q) use ($query) {
                    foreach (explode(' ', trim($query)) as $word) {
                        $q->where('product_name', 'like', '%' . $word . '%')
                            ->orWhere('tags', 'like', '%' . $word . '%')
                            ->orWhereHas('preorder_product_translations', function ($q) use ($word) {
                                $q->where('product_name', 'like', '%' . $word . '%');
                            });
                    }
                });

                $case1 = $query . '%';
                $case2 = '%' . $query . '%';

                $products->orderByRaw('CASE
                    WHEN product_name LIKE "'.$case1.'" THEN 1
                    WHEN product_name LIKE "'.$case2.'" THEN 2
                    ELSE 3
                    END');
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
            $products = $products->with('taxes')->paginate(12, ['*'], 'preorder_product')->appends(request()->query());
            
            return view('frontend.product_listing', compact('products', 'query', 'category', 'categories', 'category_id', 'brand_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attribute_values', 'colors', 'selected_color','product_type','is_available'));            
        }
        if ($brand_id != null) {
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        } elseif ($request->brand != null) {
            $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }

        $products = Product::where($conditions);

        if ($category_id != null) {
            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;
            $category = Category::with('childrenCategories')->find($category_id);

            $products = $category->products();

            $attribute_ids = AttributeCategory::whereIn('category_id', $category_ids)->pluck('attribute_id')->toArray();
            $attributes = Attribute::whereIn('id', $attribute_ids)->get();
        } else {
            $categories = Category::with('childrenCategories', 'coverImage')->where('level', 0)->orderBy('order_level', 'desc')->get();
        }

        if ($min_price != null && $max_price != null) {
            $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
        }

        if ($query != null) {
            $searchController = new SearchController;
            $searchController->store($request);

            $products->where(function ($q) use ($query) {
                foreach (explode(' ', trim($query)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                        ->orWhere('tags', 'like', '%' . $word . '%')
                        ->orWhereHas('product_translations', function ($q) use ($word) {
                            $q->where('name', 'like', '%' . $word . '%');
                        })
                        ->orWhereHas('stocks', function ($q) use ($word) {
                            $q->where('sku', 'like', '%' . $word . '%');
                        });
                }
            });

            $case1 = $query . '%';
            $case2 = '%' . $query . '%';

            $products->orderByRaw('CASE
                WHEN name LIKE "'.$case1.'" THEN 1
                WHEN name LIKE "'.$case2.'" THEN 2
                ELSE 3
                END');
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

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }

        if ($request->has('selected_attribute_values')) {
            $selected_attribute_values = $request->selected_attribute_values;
            $products->where(function ($query) use ($selected_attribute_values) {
                foreach ($selected_attribute_values as $key => $value) {
                    $str = '"' . $value . '"';

                    $query->orWhere('choice_options', 'like', '%' . $str . '%');
                }
            });
        }
        
        $products = filter_products($products)->with('taxes')->paginate(24)->appends(request()->query());

        return view('frontend.product_listing', compact('products', 'query', 'category', 'categories', 'category_id', 'brand_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attribute_values', 'colors', 'selected_color','product_type','is_available'));
    }

    public function listing(Request $request)
    {
        return $this->index($request);
    }

    public function listingByCategory(Request $request, $category_slug)
    {
        
        $category = Category::where('slug', $category_slug)->first();
        if ($category != null) {
            return $this->index($request, $category->id);
        }
        abort(404);
    }

    public function listingByBrand(Request $request, $brand_slug)
    {
        $brand = Brand::where('slug', $brand_slug)->first();
        if ($brand != null) {
            return $this->index($request, null, $brand->id);
        }
        abort(404);
    }

    //Suggestional Search
    public function ajax_search(Request $request)
    {
        $keywords = array();
        $query = $request->search;
        $preorder_products = null;
        $products = Product::where('published', 1)->where('tags', 'like', '%' . $query . '%')->get();
        foreach ($products as $key => $product) {
            foreach (explode(',', $product->tags) as $key => $tag) {
                if (stripos($tag, $query) !== false) {
                    if (sizeof($keywords) > 5) {
                        break;
                    } else {
                        if (!in_array(strtolower($tag), $keywords)) {
                            array_push($keywords, strtolower($tag));
                        }
                    }
                }
            }
        }

        $products_query = filter_products(Product::query());

        $products_query = $products_query->where('published', 1)
            ->where(function ($q) use ($query) {
                foreach (explode(' ', trim($query)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                        ->orWhere('tags', 'like', '%' . $word . '%')
                        ->orWhereHas('product_translations', function ($q) use ($word) {
                            $q->where('name', 'like', '%' . $word . '%');
                        })
                        ->orWhereHas('stocks', function ($q) use ($word) {
                            $q->where('sku', 'like', '%' . $word . '%');
                        });
                }
            });
        $case1 = $query . '%';
        $case2 = '%' . $query . '%';

        $products_query->orderByRaw('CASE
                WHEN name LIKE "'.$case1.'" THEN 1
                WHEN name LIKE "'.$case2.'" THEN 2
                ELSE 3
                END');
        $products = $products_query->limit(3)->get();

        $categories = Category::where('name', 'like', '%' . $query . '%')->get()->take(3);

        $shops = Shop::whereIn('user_id', verified_sellers_id())->where('name', 'like', '%' . $query . '%')->get()->take(3);

        if(addon_is_activated('preorder')){
            $preorder_products =  PreorderProduct::where('is_published', 1)
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('product_name', 'like', '%' . $query . '%')
                    ->orWhere('tags', 'like', '%' . $query . '%');
            })
            ->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('user_type', 'admin');
                })->orWhereHas('user.shop', function ($q) {
                    $q->where('verification_status', 1);
                });
            })
            ->limit(3)
            ->get();
            
        }

        if (sizeof($keywords) > 0 || sizeof($categories) > 0 || sizeof($products) > 0 || sizeof($shops) > 0  || sizeof($preorder_products) > 0){
            return view('frontend.partials.search_content', compact('products', 'categories', 'keywords', 'shops','preorder_products'));
        }
        return '0';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $search = Search::where('query', $request->keyword)->first();
        if ($search != null) {
            $search->count = $search->count + 1;
            $search->save();
        } else {
            $search = new Search;
            $search->query = $request->keyword;
            $search->save();
        }
    }
}
