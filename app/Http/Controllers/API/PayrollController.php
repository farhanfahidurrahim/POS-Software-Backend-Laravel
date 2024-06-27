<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Attendance;
use App\Models\LeaveManage;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payrolls = Payroll::all();
        if($payrolls->isEmpty()){
            return response()->json(['message' => 'No payroll found'], 200);
        }
        return PayrollResource::collection($payrolls);

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
    public function store(PayrollRequest $request)
    {
        try{
            $payroll = new Payroll();
            if($request->month == 1 ||$request->month == 3 ||$request->month == 5 ||$request->month == 7 ||$request->month == 8 || $request->month == 10 ||$request->month == 12){
                $end_date = date($request->month.'/31'.'/Y');
            }
            elseif($request->month == 2){
                if((date('Y')% 4 ==0 && date('Y')% 100 !=0 ) || date('Y')% 400 == 0){
                    $end_date = date($request->month.'/29'.'/Y');
                }
                else{
                    $end_date = date($request->month.'/28'.'/Y');
                }

            }
            else{
                $end_date = date($request->month.'/t'.'/Y');
            }

            //----------Date Format----------
            $dateString_startDate = $request->month.'/01/'.date('Y');
            $timestamp_startDate = strtotime($dateString_startDate);
            $start_date = date('d M y', $timestamp_startDate);

            $dateString_endDate = $end_date;
            $timestamp_endDate = strtotime($dateString_endDate);
            $end_date = date('d M y', $timestamp_endDate);

            $payroll->employee_id = $request->employee_id;
            $attendence_count = Attendance::whereBetween('date',[$start_date,$end_date])
                                ->where('employee_id',$request->employee_id)
                                ->count();
            $payroll->attendance_count = $attendence_count;
            $leave_count = LeaveManage::whereBetween('start_date',[$start_date,$end_date])
                            ->where('employee_id',$request->employee_id)
                            ->sum('total_leave');
            $payroll->leave_count = $leave_count;

            $payroll->start_date = $start_date;
            $payroll->end_date = $end_date;
            $payroll->total_amount = $request->total_amount;
            $payroll->partial_payment = $request->partial_payment;
            $payroll->due_payment = $request->total_amount- $request->partial_payment;
            $payroll->payment_status = $request->payment_status;
            $payroll->save();
            return response()->json([
                'message' => 'Payroll created successfully',
                'data' => new PayrollResource($payroll),
            ],200);
        }
        catch (\Exception $e) {
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
        $payroll = Payroll::find($id);
        if (!$payroll) {
            return response()->json(['message' => 'Payroll not found'], 404);
        }
        return new PayrollResource($payroll);
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
    public function update(Request $request, $id)
    {   
        $request->validate([
            'employee_id'     => 'required|integer',
            'total_amount'    => 'required|numeric',
            'partial_payment' => 'required|numeric',
            'payment_status'  => 'required',
        ]);

        try{
            $payroll = Payroll::find($id);
            $payroll->employee_id = $request->employee_id;
            if($request->month == 1 ||$request->month == 3 ||$request->month == 5 ||$request->month == 7 ||$request->month == 8 || $request->month == 10 ||$request->month == 12){
                $end_date = date($request->month.'/31'.'/Y');
            }
            elseif($request->month == 2){
                if((date('Y')% 4 ==0 && date('Y')% 100 !=0 ) || date('Y')% 400 == 0){
                    $end_date = date($request->month.'/29'.'/Y');
                }
                else{
                    $end_date = date($request->month.'/28'.'/Y');
                }
            }
            else{
                $end_date = date($request->month.'/t'.'/Y');
            }

            //----------Date Format----------
            // $start_date = date($request->month.'/01'.'/Y');
            $dateString_startDate = $request->month.'/01/'.date('Y');
            $timestamp_startDate = strtotime($dateString_startDate);
            $start_date = date('d M y', $timestamp_startDate);

            $dateString_endDate = $end_date;
            $timestamp_endDate = strtotime($dateString_endDate);
            $end_date = date('d M y', $timestamp_endDate);

            $attendence_count = Attendance::whereBetween('date',[$start_date,$end_date])
                                ->where('employee_id',$request->employee_id)
                                ->count();
            $payroll->attendance_count = $attendence_count;
            $leave_count = LeaveManage::whereBetween('start_date',[$start_date,$end_date])
                            ->where('employee_id',$request->employee_id)
                            ->sum('total_leave');
            $payroll->leave_count = $leave_count;
            $payroll->start_date = $start_date;
            $payroll->end_date = $end_date;
            $payroll->total_amount = $request->total_amount;
            $payroll->partial_payment = $request->partial_payment;
            $payroll->due_payment = $request->total_amount- $request->partial_payment;
            $payroll->payment_status = $request->payment_status;
            $payroll->save();
            return response()->json([
                'message' => 'Payroll updated successfully',
                'data' => new PayrollResource($payroll),
            ],200);
        }
        catch (\Exception $e) {
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
        $payroll = Payroll::find($id);

        if (!$payroll) {
            return response()->json(['message' => 'Payroll not found'], 404);
        }
        $payroll->delete();

        return response()->json(['message' => 'Payroll deleted successfully'], 200);
    }
}
