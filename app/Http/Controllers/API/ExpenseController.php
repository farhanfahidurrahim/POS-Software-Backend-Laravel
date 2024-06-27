<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Expense::latest()->get();
        if ($expenses->isEmpty()) {
            return response()->json(['message' => 'No expense found'], 200);
        }
        return ExpenseResource::collection($expenses);
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
    public function store(ExpenseRequest $request)
    {
        $deposit = Deposit::latest()->first();
        if ($deposit === null) {
            return response()->json(['message' => 'Please Expense Deposit First'],404);
        }
        
        $depositPresentBalance = $deposit->present_balance;
        
        if ($depositPresentBalance < $request->total_amount) {
            return response()->json(['message' => 'Insufficient Deposit Balance'],404);
        }

        $expense = Expense::create($request->except('expense_payment_status', 'payment_method', 'payment_account', 'payment_id'));
        if ($request->hasFile('document')) {
            $image = $request->file('document');
            $filename = time() . uniqid() . "." . $image->extension();
            $location = public_path('document/expense');
            $image->move($location, $filename);
            $expense->document = $filename;
        }
        $expense->save();

        // $deposit->present_balance -= $expense->total_amount;
        // $deposit->save();

        if ($deposit) {
            $transaction = new Transaction();
            $transaction->date = $expense->date;
            $transaction->expense_id = $expense->id;
            $transaction->transaction_type = 'expense';
            $transaction->transaction_amount = $expense->total_amount;
            $transaction->payment_status = $request->expense_payment_status;
            $transaction->save();
        }

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => new ExpenseResource($expense),
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
        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }
        return new ExpenseResource($expense);
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
    public function update(ExpenseRequest $request, $id)
    {
        $deposit = Deposit::latest()->first();
        if ($deposit === null) {
            return response()->json(['message' => 'Please Expense Deposit First'],404);
        }
        
        $depositPresentBalance = $deposit->present_balance;

        if ($depositPresentBalance < $request->total_amount) {
            return response()->json(['message' => 'Insufficient Deposit Present Balance'], 404);
        }

        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json(['message' => 'Expense not found!']);
        }

        $expense->update($request->all());


        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => new ExpenseResource($expense),
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
        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // $deposit = Deposit::latest()->first();
        // $deposit->present_balance += $expense->total_amount;
        // $deposit->save();

        $expense->delete();
        return response()->json([
            'message' => 'Expense deleted successfully',
        ], 200);
    }
}
