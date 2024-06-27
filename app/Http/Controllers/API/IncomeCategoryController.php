<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        $incomeCategories = IncomeCategory::orderBy('name', 'asc')->get();

        if ($incomeCategories->isEmpty()) {
            return response()->json(['message' => 'No Income Category found'], 200);
        }
        return response()->json([
            'message' => "Successfully fetch!",
            'data' => $incomeCategories,
        ], 200);
    }

    public function store(Request $request)
    {
        $incomeCategory = IncomeCategory::create($request->all());

        return response()->json(['message' => 'Income Category created successfully', 'data' => $incomeCategory], 200);
    }

    public function show($id)
    {
        $incomeCategory = IncomeCategory::find($id);
        if (!$incomeCategory) {
            return response()->json(['message' => 'Income Category not found'], 404);
        }
        return response()->json([
            'message' => "Successfully fetch!",
            'data' => $incomeCategory,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $incomeCategory = IncomeCategory::find($id);
        if (!$incomeCategory) {
            return response()->json(['message' => 'Expense Category not found'], 404);
        }
        $incomeCategory->update($request->all());

        return response()->json(['message' => 'Income Category updated successfully', 'data' => $incomeCategory], 200);
    }

    // public function destroy($id)
    // {
    //     $IncomeCategory = IncomeCategory::find($id);

    //     if ($IncomeCategory) {
    //         $IncomeCategory->delete();
    //         return response()->json(['message' => 'Income Category deleted successfully'], 200);
    //     }

    //     return response()->json(['message' => 'Income Category not found'], 404);
    // }
}