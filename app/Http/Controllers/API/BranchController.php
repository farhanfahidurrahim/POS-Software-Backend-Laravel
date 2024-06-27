<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Hash;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BranchResource;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        if ($branches->isEmpty()) {
            return response()->json(['message' => 'No branch found'], 200);
        }
        return BranchResource::collection($branches);
    }

    public function show($id)
    {
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }
        return new BranchResource($branch);
    }

    public function update(BranchRequest $request, $id)
    {
        $branch = Branch::find($id);
        if ($branch) {
            $branch->update($request->validated());
            return response()->json(['message' => 'Branch updated successfully', 'data' => new BranchResource($branch)], 200);
        }
        return response()->json(['message' => 'Branch not found'], 404);
    }

    // public function destroy($id)
    // {
    //     $branch = Branch::find($id);

    //         if($branch){
    //             $branch->delete();
    //             return response()->json(['message' => 'Branch deleted successfully'], 200);
    //         }

    //         return response()->json(['message' => 'Branch not found'], 404);

    // }
}
