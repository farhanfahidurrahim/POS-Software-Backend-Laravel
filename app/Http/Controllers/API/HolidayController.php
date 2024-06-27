<?php

namespace App\Http\Controllers\API;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\HolidayRequest;
use App\Http\Resources\HolidayResource;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holidays = Holiday::all();
        if($holidays->isEmpty()){
            return response()->json(['message' => 'No holiday found'], 200);
        }
        return HolidayResource::collection($holidays);
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
    public function store(HolidayRequest $request)
    {
        try {
            $holiday = new Holiday();
            $holiday->name = $request->name;
            $holiday->start_date = $request->start_date;
            $holiday->end_date = $request->end_date;
            $holiday->note = $request->note;

            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            $holiday->total_holiday = $startDate->diffInDays($endDate)+1;
            $holiday->save();

            return response()->json([
                'message' => 'Holiday created successfully',
                'data' => new HolidayResource($holiday),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Holiday creation failed',
                'error' => $e->getMessage(),
            ], 500);
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
        $holiday = Holiday::find($id);
        if (!$holiday) {
            return response()->json(['message' => 'Holiday not found'], 404);
        }
        return new HolidayResource($holiday);
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
    public function update(HolidayRequest $request, $id)
    {
        $holiday = Holiday::find($id);
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $holiday->update([
            'name' => $request->name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_holiday' => $startDate->diffInDays($endDate) + 1,
            'note' => $request->note,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Holiday updated successfully',
            'data' => new HolidayResource($holiday),
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
        $holiday = Holiday::find($id);
        if (!$holiday) {
            return response()->json(['message' => 'Holiday not found'], 404);
        }
        $holiday->delete();
        return response()->json([
            'message' => 'Holiday deleted successfully',
        ],200);
    }
}
