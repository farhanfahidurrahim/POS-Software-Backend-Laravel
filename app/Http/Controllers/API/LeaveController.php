<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest;
use App\Http\Resources\LeaveResource;
use App\Models\Leave;
use App\Models\LeaveManage;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leaves = Leave::all();
        if($leaves->isEmpty()){
            return response()->json(['message' => 'No leave found'], 200);
        }
        return LeaveResource::collection($leaves);
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
    public function store(LeaveRequest $request)
    {
        $leave = Leave::create([
            'name' => $request->name,
            'leave_count' => $request->leave_count,
        ]);

        return response()->json([
            'message' => 'Leave created successfully',
            'data' => new LeaveResource($leave),
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
        $leave = Leave::find($id);
        if (!$leave) {
            return response()->json(['message' => 'Leave not found'], 404);
        }
        return new LeaveResource($leave);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveRequest $request, $id)
    {
        $leave = Leave::find($id);

        $leave->update([
            'name' => $request->name,
            'leave_count' => $request->leave_count,
        ]);
        return response()->json([
            'message' => 'Leave updated successfully',
            'data' => new LeaveResource($leave),
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
        $leave = Leave::find($id);
        if (!$leave) {
            return response()->json(['message' => 'Leave not found'], 404);
        }
        LeaveManage::where('leave_id', $id)->get()->each->delete(); 
        $leave->delete();
        return response()->json([
            'message' => 'Leave deleted successfully',
        ],200);
    }
}
