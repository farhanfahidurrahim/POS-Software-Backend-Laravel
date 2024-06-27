<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\IncomeResource;
use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $income = Income::latest()->get();
        if ($income->isEmpty()) {
            return response()->json(['message' => 'No income found'], 200);
        }
        return IncomeResource::collection($income);
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
    public function store(Request $request)
    {
        $income = Income::create($request->all());
        // if ($request->hasFile('document')) {
        //     $image = $request->file('document');
        //     $filename = time() . uniqid() . "." . $image->extension();
        //     $location = public_path('document/expense');
        //     $image->move($location, $filename);
        //     $expense->document = $filename;
        // }
        $income->save();

        // $deposit->present_balance -= $expense->total_amount;
        // $deposit->save();

        // if ($deposit) {
        //     $transaction = new Transaction();
        //     $transaction->date = $expense->date;
        //     $transaction->expense_id = $expense->id;
        //     $transaction->transaction_type = 'expense';
        //     $transaction->transaction_amount = $expense->total_amount;
        //     $transaction->payment_status = $request->expense_payment_status;
        //     $transaction->save();
        // }

        return response()->json([
            'message' => "Successfully Created!",
            'data' => new IncomeResource($income),
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
        $income = Income::find($id);
        if (!$income) {
            return response()->json(['message' => 'Income not found'], 404);
        }
        return new IncomeResource($income);
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
    public function update(Request $request, $id)
    {
        $income = Income::find($id);
        if (!$income) {
            return response()->json(['message' => 'Income not found!']);
        }

        $income->update($request->all());

        return response()->json([
            'message' => 'Income updated successfully',
            'data' => new IncomeResource($income),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $expense = Income::find($id);
    //     if (!$expense) {
    //         return response()->json(['message' => 'Expense not found'], 404);
    //     }

    //     // $deposit = Deposit::latest()->first();
    //     // $deposit->present_balance += $expense->total_amount;
    //     // $deposit->save();

    //     $expense->delete();
    //     return response()->json([
    //         'message' => 'Expense deleted successfully',
    //     ], 200);
    // }
}
