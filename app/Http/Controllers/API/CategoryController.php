<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['children' => function ($query) {
            $query->orderBy('name', 'asc');
        }])
            ->where('parent_id', null)
            ->orderBy('name', 'asc')
            ->get();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No category found'], 200);
        }
        return CategoryResource::collection($categories);
    }

    public function getAllCategories()
    {
        $categories = Category::where('parent_id', null)
            ->orderBy('name', 'asc')
            ->get();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No category found'], 200);
        }
        return CategoryResource::collection($categories);
    }

    public function store(CategoryRequest $request)
    {

        // $category = Category::create($request->validated());
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->status = $request->status;
        $category->save();

        return response()->json(['message' => 'Category created successfully!', 'data' => new CategoryResource($category)], 200);
    }

    public function show($id)
    {
        $category = Category::with('children')->find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $id],
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->status = $request->status;
        $category->save();

        return response()->json(['message' => 'Category updated successfully!', 'data' => new CategoryResource($category)], 200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        }

        return response()->json(['message' => 'Category not found'], 404);
    }
}
