<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DesignationRequest;
use App\Http\Resources\DesignationResource;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $designations = Designation::all();
        if($designations->isEmpty()){
            return response()->json(['message' => 'No designation found'], 200);
        }
        return DesignationResource::collection($designations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DesignationRequest $request)
    {
        $designation = Designation::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Designation created successfully',
            'data' => new DesignationResource($designation),
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $designation = Designation::find($id);
        if (!$designation) {
            return response()->json(['message' => 'Designation not found'], 404);
        }
        return new DesignationResource($designation);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DesignationRequest $request, $id)
    {
        $designation = Designation::find($id);
        if (!$designation) {
            return response()->json(['message' => 'Designation not found'], 404);
        }
        $designation->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);
        return response()->json([
            'message' => 'Designation updated successfully',
            'data' => new DesignationResource($designation),
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $designation = Designation::find($id);
        if (!$designation) {
            return response()->json(['message' => 'Designation not found'], 404);
        }
        $designation->delete();
        return response()->json([
            'message' => 'Designation deleted successfully',
        ],200);
    }
}
