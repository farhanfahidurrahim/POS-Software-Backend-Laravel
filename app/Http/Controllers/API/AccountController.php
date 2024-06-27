<?php

namespace App\Http\Controllers\API;

use App\Models\Sale;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Purchase;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::latest()->get();
        if ($accounts->isEmpty()) {
            return response()->json(['message' => 'No Account Data found'], 200);
        }

        $totalExpense = Expense::select('total_amount')->sum('total_amount');
        $totalIncome = Income::select('total_amount')->sum('total_amount');

        $totalPurchaseAmount = Purchase::select('total_amount')->sum('total_amount');
        $totalReturnPurchaseAmount = PurchaseReturn::select('total_amount')->sum('total_amount');
        $totalSaleAmount = Sale::where('status', '!=', 'Cancel')->sum('total_amount');
        $totalReturnSaleAmount = SaleReturn::select('return_amount')->sum('return_amount');

        $finalSaleAmont = $totalSaleAmount - $totalReturnSaleAmount;
        $finalPurchaseAmont = $totalPurchaseAmount - $totalReturnPurchaseAmount;

        $expense_Purchase = $totalExpense +  ($totalPurchaseAmount - $totalReturnPurchaseAmount);
        $income_Sale = $totalIncome +  ($totalSaleAmount - $totalReturnSaleAmount);


        $profit_or_loss = $income_Sale - $expense_Purchase;

        return AccountResource::collection($accounts)->additional([
            'total_expense' => $totalExpense,
            'total_income' => $totalIncome,
            'final_SaleAmont' => $finalSaleAmont,
            'final_PurchaseAmont' => $finalPurchaseAmont,
            'expense_plus_purchase' => $expense_Purchase,
            'income_plus_sale' => $income_Sale,
            'profit_or_loss' => $profit_or_loss
        ]);
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
    public function store(AccountRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}