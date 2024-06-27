<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Traits\UploadAble;
use App\Http\Resources\EmployeeResource;
use App\Models\Attendance;
use App\Models\User;
use App\Models\LeaveManage;
use App\Models\TaskTarget;
use Illuminate\Support\Facades\Hash;
class EmployeeController extends Controller
{

    use UploadAble;
    public function index()
    {
        $employees = User::whereNotIn('id', [1])->latest()->get();

        if($employees->isEmpty()){
            return response()->json(['message' => 'No employee found'], 200);
        }
        return EmployeeResource::collection($employees);
    }


    public function create()
    {
        //
    }

    public function store(EmployeeRequest $request)
    {
      
        // try {
            $employee = new User();
            $employee->name = $request->name;
            $employee->last_name = $request->last_name;
            $employee->email = $request->email;
            $employee->phone_number  = $request->phone_number ;
            if ($request->hasFile('image')) {
                $filename = $this->uploadOne($request->image, 300, 300, config('imagepath.employee'));
                $employee->image = $filename;    //update new filename
                
            }
          
            $employee->password = Hash::make($request->password);
            // $employee->status = $request->status;
            $employee->user_type = $request->user_type;
           
    
            $employee->save();
    
            return response()->json([
                'message' => 'Staff created successfully',
                'data' => new EmployeeResource($employee),
            ],200);

        // } catch(\Exception $e) {
        //     // Handle the exception here
        //     return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        // }
    }
    public function show($id)
    {
        $employee = User::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
    }

    public function edit($id)
    {
        //
    }

    // public function update(EmployeeRequest $request, $id)
    // {
    //     try{

    //         $employee = Employee::find($id);
    
    //         if (!$employee) {
    //             return $this->sendError('Employee not found.');
    //         }
           
    //         if ($request->hasFile('image')){
    //             $filename = $this->uploadOne($request->image, 300, 300, config('imagepath.employee'));
    //             $this->deleteOne(config('imagepath.employee'), $employee->image);
    //             $employee->update(['image' => $filename]);
    //         }
    
    
    //         $employee->first_name = $request->first_name;
    //         $employee->last_name = $request->last_name;
    //         $employee->designation_id = $request->designation_id;
    //         $employee->email = $request->email;
    //         $employee->phone_number = $request->phone_number;
    //         $employee->family_number = $request->family_number;
    //         $employee->nid = $request->nid;
    //         $employee->passport = $request->passport;
    //         $employee->dob = $request->dob;
    //         $employee->gender = $request->gender;
    //         $employee->marital_status = $request->marital_status;
    //         $employee->blood_group = $request->blood_group;
    //         $employee->permanent_address = $request->permanent_address;
    //         $employee->bank_details = $request->bank_details;
    //         $employee->status = $request->status;
    
    //         $employee->update();
    
    //         return response()->json([
    //             'message' => 'Employee Updated successfully',
    //             'data' => new EmployeeResource($employee),
    //         ],200);
    //     }
    //     catch(\Exception $e) {
    //         // Handle the exception here
    //         return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = User::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        // Attendance::where('employee_id', $id)->get()->each->delete();       
        // TaskTarget::where('employee_id', $id)->get()->each->delete();       
        // LeaveManage::where('employee_id', $id)->get()->each->delete();       
        $employee->delete();
        return response()->json([
            'message' => 'Staff deleted successfully',
        ],200);
    }

}
