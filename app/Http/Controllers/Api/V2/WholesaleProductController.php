<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\WholesaleProductCollection;
use App\Http\Resources\V2\Seller\WholesaleProductDetailsCollection;
use Illuminate\Http\Request;
use CoreComponentRepository;
use App\Models\Product;

class WholesaleProductController extends Controller
{
    public function all_wholesale_products(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        $products = Product::where('wholesale_product', 1)->orderBy('created_at', 'desc');

        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
        }

        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $products->paginate(15);

        return response()->json([
            'result' => true,
            'products' => new WholesaleProductCollection($products),
        ], 200);
    }
    public function wholesale_product_details(Request $request, $id)
    {
        $product =  Product::find($id);
        return new WholesaleProductDetailsCollection($product);
    }
}
