<?php

namespace App\Http\Controllers\API;

use App\Models\Sale;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffProductCount = [];
        $staffSaleCount = [];
        $staffPurchaseCount = [];
        if(Auth::user()->user_type != 1){
            $staffProductCount = Product::where('created_by', Auth::user()->id)->count(); 
        }

        if(Auth::user()->user_type != 1){
            $staffSaleCount = Sale::where('created_by', Auth::user()->id)->count();
        }
        if(Auth::user()->user_type != 1){
            $staffPurchaseCount = Purchase::where('created_by', Auth::user()->id)->count();
        }

        $productCount = Product::count();
        $saleCount = Sale::count();
        $customerCount = Customer::count();
        $supplierCount = Supplier::count();
        $purchaseCount = Purchase::count();

        $totalSaleAmount = Sale::select('total_amount')->sum('total_amount');
        $totalPaidSaleAmount = Sale::select('paid_amount')->sum('paid_amount');
        $totalDueSaleAmount = Sale::select('due_amount')->sum('due_amount');
        $totalReturnSaleAmount = SaleReturn::select('return_amount')->sum('return_amount');

        $totalPurchaseAmount = Purchase::select('total_amount')->sum('total_amount');
        $totalPaidPurchaseAmount = Purchase::select('paid_amount')->sum('paid_amount');
        $totalDuePurchaseAmount = Purchase::select('due_amount')->sum('due_amount');
        $totalReturnPurchaseAmount = PurchaseReturn::select('total_amount')->sum('total_amount');

        // Get month-wise sale amounts for the current year
        $currentYear = Carbon::now()->year;
        $monthlySaleAmounts = Sale::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_amount) as total_sale_amount'))
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        // Get month-wise purchase amounts for the current year
        $monthlyPurchaseAmounts = Purchase::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_amount) as total_purchase_amount'))
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        // Merge sale and purchase data & Combine the data for the current year
        $combinedData = [];
        foreach ($monthNames as $monthNumber => $monthName) {
            $saleData = $monthlySaleAmounts->where('month', $monthNumber)->first();
            $purchaseData = $monthlyPurchaseAmounts->where('month', $monthNumber)->first();

            $saleAmount = $saleData ? $saleData->total_sale_amount : 0;
            $purchaseAmount = $purchaseData ? $purchaseData->total_purchase_amount : 0;

            $combinedData[] = [
                'month' => $monthName,
                'monthly_sale_amount' => $saleAmount,
                'monthly_purchase_amount' => $purchaseAmount,
            ];
        }

        //Profit or Loss
        $account = Account::first();
        $availableBalance = $account->available_balance;
        $totalExpenseAmount = Expense::select('total_amount')->sum('total_amount');
        $profit = ($totalSaleAmount - $totalReturnSaleAmount) - (($totalPurchaseAmount - $totalReturnPurchaseAmount) + $totalExpenseAmount);

        return response()->json([
            'product_count' => $productCount,
            'staffProductCount' => $staffProductCount,
            'staffSaleCount' => $staffSaleCount,
            'staffPurchaseCount' => $staffPurchaseCount,
            'sale_count' => $saleCount,
            'customer_count' => $customerCount,
            'supplier_count' => $supplierCount,
            'purchase_count' => $purchaseCount,

            'total_sale_amount' => $totalSaleAmount,
            'total_paid_sale_amount' => $totalPaidSaleAmount,
            'total_due_sale_amount' => $totalDueSaleAmount,
            'total_return_sale_amount' => $totalReturnSaleAmount,

            'total_purchase_amount' => $totalPurchaseAmount,
            'total_paid_purchase_amount' => $totalPaidPurchaseAmount,
            'total_due_purchase_amount' => $totalDuePurchaseAmount,
            'total_return_purchase_amount' => $totalReturnPurchaseAmount,

            'monthly_current_year' => $combinedData,

            'available_balance' => $availableBalance,
            'total_expense_amount' => $totalExpenseAmount,
            'profit' => $profit,
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
    public function store(Request $request)
    {
        //
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
