<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VariationResource;
use App\Models\VariationValueTemplate;
use App\Traits\UploadAble;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Variation;
use App\Models\VariationTemplate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

    use UploadAble;
    public function index()
    {
        // if (Auth::user()->user_type == 2) {
        //     $products = Product::where('created_by', Auth::user()->id)->latest()->get();
        // } else {
        //     // For admins or other user types, retrieve all sales data
        //     $products = Product::latest()->get();
        // }

        $products = Product::latest()->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No Product found'], 200);
        }
        return ProductResource::collection($products);
    }

    public function store(ProductRequest $request)
    {
        // dd($request->all());
        try {
            $product = new Product();
            if ($request->hasFile('image')) {
                $filename = $this->uploadOne($request->image, 445, 534, config('imagepath.product'));
                $product->image = $filename;
            }
            $product->name = $request->name;
            $product->detail = $request->detail;
            //create random sku with name
            $baseName = Str::of($request->name)->slug('-');
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $product->sku = $baseName . $randomNumber;
            $product->unit_id = $request->unit_id;
            $product->brand_id = $request->brand_id;
            $product->sub_unit_ids = $request->sub_unit_ids;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->type = $request->type;
            $product->status = $request->status;
            $product->created_by = Auth::user()->id;
            $product->save();

            if ($request->type == 'single') {
                // Create a variant for single products
                // $variation_template_name = VariationTemplate::find($request->variation_template_id[0]);
                // $variation_template_value = VariationValueTemplate::find($request->variation_value_id[0]);

                $variation_template_name = $request->variation_template_id[0] ? VariationTemplate::find($request->variation_template_id[0]) : null;
                // $variation_template_value = $request->variation_value_id[0] ? VariationValueTemplate::find($request->variation_value_id[0]) : null;
                $variation_template_value = "1";

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->sub_sku = $request->sku;
                // $variantData->name = $product->name . "-" . $variation_template_name->name . "-" . $variation_template_value->name;
                // $variantData->name = $product->name . "-" . optional($variation_template_value)->name;
                $variantData->name = $product->name;
                $variantData->product_barcode = Str::random(15);
                $variantData->default_purchase_price = $request->default_purchase_price[0];
                $variantData->profit_percent = $request->profit_percent[0];
                $variantData->default_sell_price = $request->default_sell_price[0];
                $variantData->variation_value_id = $request->variation_value_id[0];
                $variantData->variation_template_id = $request->variation_template_id[0];
                $variantData->stock_amount = isset($request->stock_amount[0]) ? $request->stock_amount[0] : null;
                $variantData->alert_quantity = $request->alert_quantity[0];
                if ($request->hasFile('images')) {
                    $filename = $this->uploadOne($request->images[0], 445, 534, config('imagepath.product_variation'));
                    $variantData->images = $filename;
                }
                $variantData->save();
            } elseif ($request->type == 'variable') {

                for ($x = 0; $x < count($request->profit_percent); $x++) {
                    $variation_template_name = VariationTemplate::find($request->variation_template_id[$x]);
                    $variation_template_value = VariationValueTemplate::find($request->variation_value_id[$x]);
                    $variantData = new Variation();
                    $variantData->product_id = $product->id;
                    $variantData->brand_id = $product->brand_id;
                    $variantData->category_id = $product->category_id;
                    $variantData->name = $product->name . "-" . $variation_template_value->name;
                    $variantData->sub_sku = $request->sku;
                    $variantData->product_barcode = Str::random(15);
                    $variantData->default_purchase_price = $request->default_purchase_price[$x];
                    $variantData->profit_percent = $request->profit_percent[$x];
                    $variantData->default_sell_price = $request->default_sell_price[$x];
                    $variantData->variation_value_id = $request->variation_value_id[$x];
                    $variantData->variation_template_id = $request->variation_template_id[$x];
                    $variantData->stock_amount = $request->stock_amount[$x];
                    $variantData->alert_quantity = $request->alert_quantity[$x];
                    if ($request->hasFile('images')) {
                        $filename = $this->uploadOne($request->images[$x], 445, 534, config('imagepath.product_variation'));
                        $variantData->images = $filename;
                    }
                    $variantData->save();
                }
            }
            return response()->json(['message' => 'Product created successfully', 'data' => new ProductResource($product)], 200);
        } catch (\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return new ProductResource($product);
    }

    ////////////////////// Product Update /////////////////////////

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            // Delete old image if it exists
            if ($request->hasFile('image') && $product->image) {
                $oldImagePath = public_path(config('imagepath.product') . $product->image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }
            // Updated image upload
            if ($request->hasFile('image')) {
                $filename = $this->uploadOne($request->image, 445, 534, config('imagepath.product'));
                $product->image = $filename;
            }

            $product->name = $request->name;
            $product->detail = $request->detail;
            $product->sku = $request->sku;
            $product->unit_id = $request->unit_id;
            $product->sub_unit_ids = $request->sub_unit_ids;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->type = $request->type;
            $product->status = $request->status;
            $product->created_by = Auth::user()->id;
            $product->update();
            if ($request->type == 'single') {
                if ($request->variation_id[0] == null) {
                    $variation_template_name = VariationTemplate::find($request->variation_template_id[0]);
                    $variation_template_value = VariationValueTemplate::find($request->variation_value_id[0]);
                    $variantData = new Variation();
                    $variantData->product_id = $product->id;
                    $variantData->sub_sku = $request->sku;
                    $variantData->name = $product->name . "-" . $variation_template_value->name;
                    $variantData->product_barcode = Str::random(15);
                    $variantData->default_purchase_price = $request->default_purchase_price[0];
                    $variantData->profit_percent = $request->profit_percent[0];
                    $variantData->default_sell_price = $request->default_sell_price[0];
                    $variantData->variation_value_id = $request->variation_value_id[0];
                    $variantData->variation_template_id = $request->variation_template_id[0];
                    $variantData->stock_amount = $request->stock_amount[0];
                    $variantData->alert_quantity = $request->alert_quantity[0];

                    // Delete old image if it exists
                    if ($request->hasFile('images') && $variantData->images) {
                        $oldImagePath = public_path(config('imagepath.product_variation') . $variantData->images);
                        if (File::exists($oldImagePath)) {
                            File::delete($oldImagePath);
                        }
                    }
                    // Updated image upload
                    if ($request->hasFile('images')) {
                        $filename = $this->uploadOne($request->images[0], 445, 534, config('imagepath.product_variation'));
                        $variantData->images = $filename;
                    }
                    $variantData->save();
                } else {

                    $variation_template_name = VariationTemplate::find($request->variation_template_id[0]);
                    $variation_template_value = VariationValueTemplate::find($request->variation_value_id[0]);
                    $variantData = Variation::find($request->variation_id[0]);
                    $variantData->product_id = $product->id;
                    $variantData->sub_sku = $request->sku;
                    $variantData->name = $product->name . "-" . $variation_template_value->name;
                    $variantData->default_purchase_price = $request->default_purchase_price[0];
                    $variantData->profit_percent = $request->profit_percent[0];
                    $variantData->default_sell_price = $request->default_sell_price[0];
                    $variantData->variation_value_id = $request->variation_value_id[0];
                    $variantData->variation_template_id = $request->variation_template_id[0];
                    $variantData->stock_amount = $request->stock_amount[0];
                    $variantData->alert_quantity = $request->alert_quantity[0];

                    // Delete old image if it exists
                    if ($request->hasFile('images') && $variantData->images) {
                        $oldImagePath = public_path(config('imagepath.product_variation') . $variantData->images);
                        if (File::exists($oldImagePath)) {
                            File::delete($oldImagePath);
                        }
                    }
                    if ($request->hasFile('images')) {
                        $filename = $this->uploadOne($request->images[0], 445, 534, config('imagepath.product_variation'));
                        $variantData->images = $filename;
                    }
                    $variantData->update();
                }
            } elseif ($request->type == 'variable') {

                for ($x = 0; $x < count($request->profit_percent); $x++) {
                    if ($request->variation_id[$x] == "null") {
                        $variation_template_name = VariationTemplate::find($request->variation_template_id[$x]);
                        $variation_template_value = VariationValueTemplate::find($request->variation_value_id[$x]);

                        $variantData = new Variation();
                        $variantData->product_id = $product->id;
                        $variantData->name = $product->name . "-" . $variation_template_value->name;
                        $variantData->sub_sku = $request->sku;
                        $variantData->product_barcode = Str::random(15);
                        $variantData->default_purchase_price = $request->default_purchase_price[$x];
                        $variantData->profit_percent = $request->profit_percent[$x];
                        $variantData->default_sell_price = $request->default_sell_price[$x];
                        $variantData->variation_value_id = $request->variation_value_id[$x];
                        $variantData->variation_template_id = $request->variation_template_id[$x];
                        $variantData->stock_amount = $request->stock_amount[$x];
                        $variantData->alert_quantity = $request->alert_quantity[$x];

                        // Delete old image if it exists
                        if ($request->hasFile('images') && $variantData->images) {
                            $oldImagePath = public_path(config('imagepath.product_variation') . $variantData->images);
                            if (File::exists($oldImagePath)) {
                                File::delete($oldImagePath);
                            }
                        }
                        // Updated image upload
                        if ($request->hasFile('images') && ($request->file('images')[$x] ?? null) && $request->file('images')[$x]->isValid()) {
                            $filename = $this->uploadOne($request->images[$x], 445, 534, config('imagepath.product_variation'));
                            $variantData->images = $filename;
                        }
                        //dd($variantData);
                        $variantData->save();
                    } else {

                        $variation_template_name = VariationTemplate::find($request->variation_template_id[$x]);
                        $variation_template_value = VariationValueTemplate::find($request->variation_value_id[$x]);

                        $variantData = Variation::find($request->variation_id[$x]);
                        $variantData->product_id = $product->id;
                        $variantData->name = $product->name . "-" . $variation_template_value->name;
                        $variantData->sub_sku = $request->sku;
                        $variantData->default_purchase_price = $request->default_purchase_price[$x];
                        $variantData->profit_percent = $request->profit_percent[$x];
                        $variantData->default_sell_price = $request->default_sell_price[$x];
                        $variantData->variation_value_id = $request->variation_value_id[$x];
                        $variantData->variation_template_id = $request->variation_template_id[$x];
                        $variantData->stock_amount = $request->stock_amount[$x];
                        $variantData->alert_quantity = $request->alert_quantity[$x];

                        // Delete old image if it exists
                        if ($request->hasFile('images') && $variantData->images) {
                            $oldImagePath = public_path(config('imagepath.product_variation') . $variantData->images);
                            if (File::exists($oldImagePath)) {
                                File::delete($oldImagePath);
                            }
                        }
                        // Updated image upload
                        if ($request->hasFile('images') && ($request->file('images')[$x] ?? null) && $request->file('images')[$x]->isValid()) {
                            $filename = $this->uploadOne($request->images[$x], 445, 534, config('imagepath.product_variation'));
                            $variantData->images = $filename;
                        }
                        //dd($variantData);
                        $variantData->update();
                    }
                }
            }

            return response()->json(['message' => 'Product updated successfully'], 200);
        } catch (\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $productVariations = $product->productVariations;
        foreach ($productVariations as $productVariation) {
            $productVariation->variations()->delete();
        }
        $product->productVariations()->delete();
        $product->delete();

        return response()->json(['message' => 'Product and related data deleted successfully'], 200);
    }

    public function brandWiseProduct(Request $request, $brand_id)
    {
        $products = Product::where('brand_id', $brand_id)->get();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No Brand Product Data Found!'], 404);
        } else {
            return response()->json([
                'brand_wise_product' => $products,
            ], 200);
        }
    }

    public function categoryWiseProduct(Request $request, $category_id)
    {
        $products = Product::where('category_id', $category_id)->get();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No Category Product Data Found!'], 404);
        } else {
            return response()->json([
                'category_wise_product' => $products,
            ], 200);
        }
    }
}
