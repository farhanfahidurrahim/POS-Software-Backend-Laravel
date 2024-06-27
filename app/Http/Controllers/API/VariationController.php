<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VariationPosResource;

class VariationController extends Controller
{
    /* ====== <<< get_all_variation_where_product_status_active  >>> ====== */
    public function allVariations()
    {
        $products = Product::where('status', 'active')->get();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No active Products found'], 404);
        }

        $variations = Variation::whereIn('product_id',  $products->pluck('id'))->latest()->get();
        if ($variations->isEmpty()) {
            return response()->json(['message' => 'No Variation found'], 200);
        }

        //return VariationResource::collection($variations);
        return VariationPosResource::collection($variations);
    }
    public function paginateVariations()
    {
        $products = Product::where('status', 'active')->get();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No active Products found'], 404);
        }

        $variations = Variation::whereIn('product_id',  $products->pluck('id'))->latest()->paginate(6);
        if ($variations->isEmpty()) {
            return response()->json(['message' => 'No Variation found'], 200);
        }
        
        //return VariationResource::collection($variations);
        return VariationPosResource::collection($variations);
    }
}
