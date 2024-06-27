<?php

namespace App\Http\Controllers\API;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Requests\UnitRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->get();
        if ($units->isEmpty()) {
            return response()->json(['message' => 'No unit found!'], 200);
        }
        return UnitResource::collection($units);
    }

    public function store(UnitRequest $request)
    {
        // try{
        //     $unit = Unit::create($request->all());
        //     $unit->created_by = Auth::user()->id;
        //     $unit->save();
        //     return response()->json(['message' => 'Unit created successfully', 'data' => new UnitResource($unit)], 200);
        // }
        // catch (\Exception $e) {
        //     // Handle the exception here
        //     return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        // }

        $unit = $request->all();
        $unit['user_id'] = Auth::user()->id;
        $unit = Unit::create($unit);

        return response()->json(['message' => 'Unit created successfully!', 'data' => new UnitResource($unit)], 200);
    }
    public function show($id)
    {
        $unit = Unit::find($id);
        if (!$unit) {
            return response()->json(['message' => 'Unit not found'], 404);
        }
        return new UnitResource($unit);
    }

    public function update(UnitRequest $request, $id)
    {
        try {
            $unit = Unit::find($id);
            if (!$unit) {
                return response()->json(['message' => 'Unit not found'], 404);
            }
            $unit->update($request->all());
            $unit->user_id = Auth::user()->id;
            $unit->update();
            return response()->json(['message' => 'Unit updated successfully!', 'data' => new UnitResource($unit)], 200);
        } catch (\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        $unit = Unit::find($id);
        if ($unit) {
            $unit->delete();
            return response()->json(['message' => 'Unit deleted successfully'], 200);
        }
        return response()->json(['message' => 'Unit not found'], 404);
    }
}
