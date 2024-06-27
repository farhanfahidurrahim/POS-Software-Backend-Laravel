<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function index()
    {
        // $brands = Brand::latest()->get();
        $brands = Brand::orderBy('name', 'asc')->get();
        if($brands->isEmpty()){
            return response()->json(['message' => 'No brands found'], 200);
        }
        return BrandResource::collection($brands);
    }

    public function store(BrandRequest $request)
    {
        try {
            $brand = new Brand();
            $brand->category_id = $request->category_id;
            $brand->name = $request->name;
            $brand->description = $request->description;
            $brand->created_by = Auth::id();
            $brand->save();

            return response()->json(['message' => 'Brand created successfully', 'data' => new BrandResource($brand)], 200);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
        return new BrandResource($brand);
    }

    public function update(BrandRequest $request, $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
        $brand->update($request->validated());

        return response()->json(['message' => 'Brand updated successfully', 'data' => new BrandResource($brand)], 200);
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);

            if($brand){
                $brand->delete();
                return response()->json(['message' => 'Brand deleted successfully'], 200);
            }

        return response()->json(['message' => 'Brand not found'], 404);

    }
}
