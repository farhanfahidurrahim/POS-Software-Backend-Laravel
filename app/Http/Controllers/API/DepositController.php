<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepositResource;
use App\Models\Deposit;
use App\Models\Account;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deposit = Deposit::all();
        if ($deposit->isEmpty()) {
            return response()->json(['message' => 'No deposit found'], 200);
        }

        $totalDeposit = Deposit::select('present_balance')->latest()->first();
        $totalExpense = Expense::select('total_amount')->sum('total_amount');

        //return DepositResource::collection($deposit);

        return DepositResource::collection($deposit)->additional(['total_deposit' => $totalDeposit, 'total_expense' => $totalExpense]);

        // return response()->json([
        //     'data' => $deposit,
        //     'total_expense' => $totalExpense,
        // ]);
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
        $request->validate([
            'amount' => 'required|numeric|between:1,10000000000000',
            'note' => 'nullable|max:500',
        ]);

        /* === <<< Account1 >>> ===*/
        $account = Account::first();
        $accountBalance = $account->available_balance;
        if ($accountBalance < $request->amount) {
            return response()->json(['message' => 'Insufficient Account Balance']);
        }

        /* === <<< Deposit Create >>> ===*/
        $depositLists = Deposit::all();
        if (count($depositLists) == 0) {
            $deposit = Deposit::create([
                'created_by' => Auth::user()->id,
                'amount' =>  $request->amount,
                'previous_balance' => "0.00",
                'present_balance' => $request->amount,
                'note' => $request->note ?? '',
            ]);
        } else {
            $latestDeposit = Deposit::latest()->first();
            $deposit = Deposit::create([
                'created_by' => Auth::user()->id,
                'amount' =>  $request->amount,
                'present_balance' => $latestDeposit->present_balance + $request->amount,
                'previous_balance' => $latestDeposit->present_balance,
                'note' => $request->note ?? '',
            ]);
        }

        /* === <<< Account2 >>> ===*/
        $account->available_balance -= $request->amount;
        $account->save();

        return response()->json([
            'message' => 'Deposit created successfully',
            'data' => new DepositResource($deposit),
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
        $deposit = Deposit::find($id);
        if (!$deposit) {
            return response()->json(['message' => 'Deposit not found'], 404);
        }
        return new DepositResource($deposit);
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
            'amount' => 'required|numeric|between:1,10000000000000',
            'note' => 'nullable|max:500',
        ]);

        $beforeDeposit = Deposit::find($id);
        if (!$beforeDeposit) {
            return response()->json(['message' => 'Deposit not found!']);
        }

        $account = Account::first();
        $accountBalance = $account->available_balance;
        if ($accountBalance < $request->amount) {
            return response()->json(['message' => 'Insufficient Account Balance']);
        }

        $beforeDeposit->present_balance -= $request->previous_amount;
        $beforeDeposit->save();

        /* === <<< Account >>> ===*/

        $account->available_balance += $beforeDeposit->amount;
        $account->save();

        $latestDeposit = Deposit::latest()->skip(1)->first();
        $deposit = Deposit::findOrFail($id);
        if ($latestDeposit) {
            $deposit->update([
                'created_by' => Auth::user()->id,
                'amount' =>  $request->amount,
                'present_balance' => $latestDeposit->present_balance + $request->amount,
                'previous_balance' => $latestDeposit->present_balance,
                'note' => $request->note ?? '',
            ]);
        } else {
            $deposit->update([
                'created_by' => Auth::user()->id,
                'amount' =>  $request->amount,
                'present_balance' => $request->amount,
                'previous_balance' => "0.00",
                'note' => $request->note ?? '',
            ]);
        }

        /* === <<< Account >>> ===*/
        $account = Account::first();
        $account->available_balance -= $request->amount;
        $account->save();

        return response()->json([
            'message' => 'Deposit updated successfully!',
            'data' => new DepositResource($deposit),
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
        $deposit = Deposit::find($id);

        if (!$deposit) {
            return response()->json(['message' => 'Deposit not found!'], 404);
        }

        $account = Account::first();
        $account->available_balance += $deposit->amount;
        $account->save();

        $deposit->delete();
        return response()->json([
            'message' => 'Deposit deleted successfully',
        ], 200);
    }
}
