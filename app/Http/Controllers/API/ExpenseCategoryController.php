<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseCategoryRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $expenseCategories = ExpenseCategory::orderBy('name', 'asc')->get();

        if ($expenseCategories->isEmpty()) {
            return response()->json(['message' => 'No Expense Category found'], 200);
        }
        return ExpenseCategoryResource::collection($expenseCategories);
    }

    public function store(ExpenseCategoryRequest $request)
    {

        $expenseCategory = ExpenseCategory::create($request->validated());

        return response()->json(['message' => 'Expense Category created successfully', 'data' => new ExpenseCategoryResource($expenseCategory)], 200);
    }

    public function show($id)
    {
        $expenseCategory = ExpenseCategory::find($id);
        if (!$expenseCategory) {
            return response()->json(['message' => 'Expense Category not found'], 404);
        }
        return new ExpenseCategoryResource($expenseCategory);
    }

    public function update(ExpenseCategoryRequest $request, $id)
    {
        $expenseCategory = ExpenseCategory::find($id);
        if (!$expenseCategory) {
            return response()->json(['message' => 'Expense Category not found'], 404);
        }
        $expenseCategory->update($request->validated());

        return response()->json(['message' => 'Expense Category updated successfully', 'data' => new ExpenseCategoryResource($expenseCategory)], 200);
    }

    // public function destroy($id)
    // {
    //     $expenseCategory = ExpenseCategory::find($id);

    //         if($expenseCategory){
    //             $expenseCategory->delete();
    //             return response()->json(['message' => 'Expense Category deleted successfully'], 200);
    //         }

    //         return response()->json(['message' => 'Expense Category not found'], 404);

    // }
}
