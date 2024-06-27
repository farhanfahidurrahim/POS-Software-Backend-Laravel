<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveManageRequest;
use App\Http\Resources\LeaveManageResource;
use App\Models\LeaveManage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LeaveManageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leavemanages = LeaveManage::all();
        if($leavemanages->isEmpty()){
            return response()->json(['message' => 'No leave manage found'], 200);
        }
        return LeaveManageResource::collection($leavemanages);
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
    public function store(LeaveManageRequest $request)
    {
        try{
            $leavemanage = new LeaveManage();
            $leavemanage->employee_id = $request->employee_id;
            $leavemanage->leave_id = $request->leave_id;
            $leavemanage->start_date = $request->start_date;
            $leavemanage->end_date = $request->end_date;
            $leavemanage->reason = $request->reason;
            $start_date = \Carbon\Carbon::parse($request->start_date);
            $end_date = \Carbon\Carbon::parse($request->end_date);
            $leavemanage->total_leave = $end_date->diffInDays($start_date)+1;
            if ($request->hasFile('document')) {
                $image = $request->file('document');
                $filename = time() . uniqid() . "." . $image->extension();
                $location = public_path('document/leavemanage');
                $image->move($location, $filename);
                $leavemanage->document = $filename;
            }
            $leavemanage->status = $request->status;
            $leavemanage->save();
            return response()->json([
                'message' => 'Leave Manage created successfully',
                'data' => new LeaveManageResource($leavemanage),
            ],200);
        }
        catch(\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leavemanage = LeaveManage::find($id);
        if (!$leavemanage) {
            return response()->json(['message' => 'Leave Manage not found'], 404);
        }
        return new LeaveManageResource($leavemanage);
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
    public function update(LeaveManageRequest $request, $id)
    {
        try{

            $leavemanage = LeaveManage::find($id);
            if (!$leavemanage) {
                return response()->json(['message' => 'Leave Manage not found'], 404);
            }
            $leavemanage->employee_id = $request->employee_id;
            $leavemanage->leave_id = $request->leave_id;
            $leavemanage->start_date = $request->start_date;
            $leavemanage->end_date = $request->end_date;
            $leavemanage->reason = $request->reason;
            $start_date = \Carbon\Carbon::parse($request->start_date);
            $end_date = \Carbon\Carbon::parse($request->end_date);
            $leavemanage->total_leave = $end_date->diffInDays($start_date) + 1;
            if ($request->hasFile('document')) {
                $image = $request->file('document');
                $filename = time() . uniqid() . "." . $image->extension();
                $location = public_path('document/leavemanage');
                $image->move($location, $filename);
                $leavemanage->document = $filename;
            }
            $leavemanage->status = $request->status;
            $leavemanage->save();
            return response()->json([
                'message' => 'Leave Manage updated successfully',
                'data' => new LeaveManageResource($leavemanage),
            ],200);
        }
        catch(\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leavemanage = LeaveManage::find($id);
        if (!$leavemanage) {
            return response()->json(['message' => 'Leave manage not found'], 404);
        }
        $leavemanage->delete();
        return response()->json([
            'message' => 'Leave manage deleted successfully',
        ],200);
    }
}
