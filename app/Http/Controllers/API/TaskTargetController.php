<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskTargetRequest;
use App\Http\Resources\TaskTargetResource;
use App\Models\TaskTarget;
use Illuminate\Http\Request;

class TaskTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskTargets = TaskTarget::all();
        if($taskTargets->isEmpty()){
            return response()->json(['message' => 'No task Target found'], 200);
        }
        return TaskTargetResource::collection($taskTargets);
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
    public function store(TaskTargetRequest $request)
    {
        $taskTarget = TaskTarget::create([
            'employee_id' => $request->employee_id,
            'description' => $request->description,
        ]);
        $taskTarget->employee;
        return response()->json([
            'message' => 'Task Target created successfully',
            'data' => new TaskTargetResource($taskTarget),
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
        $taskTarget = TaskTarget::find($id);
        if (!$taskTarget) {
            return response()->json(['message' => 'Task Target not found'], 404);
        }

        return new TaskTargetResource($taskTarget);
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
    public function update(TaskTargetRequest $request, $id)
    {
        $taskTarget = TaskTarget::find($id);
        $taskTarget->update([
            'employee_id' => $request->employee_id,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Task Target updated successfully',
            'data' => new TaskTargetResource($taskTarget),
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
        $taskTarget = TaskTarget::find($id);
        if (!$taskTarget) {
            return response()->json(['message' => 'Task Target not found'], 404);
        }
        $taskTarget->delete();
        return response()->json([
            'message' => 'Task Target deleted successfully',
        ],200);
    }
}
