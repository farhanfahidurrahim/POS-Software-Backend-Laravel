<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendances = Attendance::latest()->get();
        if ($attendances->isEmpty()) {
            return response()->json(['message' => 'No attendance found'], 200);
        }
        return AttendanceResource::collection($attendances);
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
    public function store(AttendanceRequest $request)
    {
        $attendance = new Attendance();

        $attendance->employee_id = $request->employee_id;
        $attendance->shift_id = $request->shift_id;
        $attendance->date = $request->date;
        $attendance->save();

        return response()->json([
            'message' => 'Attendance created successfully',
            'data' => new AttendanceResource($attendance),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }
        return new AttendanceResource($attendance);
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
    public function update(AttendanceRequest $request, $id)
    {
        $attendance = Attendance::find($id);

        $attendance->employee_id = $request->employee_id;
        $attendance->shift_id = $request->shift_id;
        $attendance->date = $request->date;
        if ($request->hasFile('document')) {
            $image = $request->file('document');
            $filename = time() . uniqid() . "." . $image->extension();
            $location = public_path('document/attendance');
            $image->move($location, $filename);
            $attendance->document = $filename;
        }
        $attendance->update();
        return response()->json([
            'message' => 'Attendance updated successfully',
            'data' => new AttendanceResource($attendance),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }
        $attendance->delete();
        return response()->json([
            'message' => 'Attendance deleted successfully',
        ], 200);
    }
}
