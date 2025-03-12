<?php

namespace App\Helpers;

use App\Models\Product;

class ProductHelper
{
    public static function extractSaltComposition($description)
    {
        $salts = [];
        
        if (preg_match('/SALT COMPOSITION.*?<p.*?>(.*?)<\/p>/is', $description, $match)) {
            $saltSection = $match[1];
            preg_match_all('/([^:]+):\s*([\d.]+\s*gm)/i', $saltSection, $saltMatches);
            
            for ($i = 0; $i < count($saltMatches[1]); $i++) {
                $saltName = trim($saltMatches[1][$i]);
                $saltAmount = trim($saltMatches[2][$i]);
                $salts[$saltName] = $saltAmount;
            }
        }
        
        return $salts;
    }

    public static function getSubstituteProducts($currentProduct, $limit = 5)
    {
        $currentSalts = self::extractSaltComposition($currentProduct->description);
        
        if (empty($currentSalts)) {
            return collect(); // Return empty collection if no salts found
        }

        $substituteProducts = Product::with('brand') // Assuming brand relation exists
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

                // Compare salts (considering both name and quantity)
                $matchingSalts = array_intersect_assoc($currentSalts, $productSalts);
                return count($matchingSalts) === count($currentSalts) && 
                       count($matchingSalts) === count($productSalts);
            })
            ->map(function ($product) use ($currentProduct) {
                // Add price comparison and manufacturer data
                $product->price_comparison = self::calculatePriceComparison($product, $currentProduct);
                $product->manufacturer = $product->brand ? $product->brand->name : 'Unknown Manufacturer';
                return $product;
            })
            ->take($limit);

        return $substituteProducts;
    }

    public static function calculatePriceComparison($substituteProduct, $currentProduct)
    {
        $currentPrice = $currentProduct->unit_price ?? 0;
        $substitutePrice = $substituteProduct->unit_price ?? 0;

        if ($currentPrice <= 0) {
            return ['percentage' => 0, 'is_cheaper' => false];
        }

        $difference = ($substitutePrice - $currentPrice) / $currentPrice * 100;
        $isCheaper = $difference < 0;

        return [
            'percentage' => number_format(abs($difference), 1),
            'is_cheaper' => $isCheaper
        ];
    }
}