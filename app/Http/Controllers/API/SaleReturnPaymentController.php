<?php

namespace App\Http\Controllers\API;

use App\Models\Branch;
use App\Models\Account;
use App\Models\SaleReturn;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SaleReturnPayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SaleReturnPaymentResource;

class SaleReturnPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if (Auth::user()->user_type == 2) {
        //     $saleReturnPayment = SaleReturnPayment::where('created_by', Auth::user()->id)->latest()->get();
        // } else {
        //     // For admins or other user types, retrieve all sales data
        //     $saleReturnPayment = SaleReturnPayment::latest()->get();
        // }

        $saleReturnPayment = SaleReturnPayment::latest()->get();
        
        if ($saleReturnPayment->isEmpty()) {
            return response()->json(['message' => 'No Sale Return Payment found'], 200);
        }
        return SaleReturnPaymentResource::collection($saleReturnPayment);
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
        try{
            $id = $request->sale_return_id;
            $saleReturn = SaleReturn::find($id);

            if (!$saleReturn) {
                return response()->json(['message' => 'No Sale Return found'], 404);
            }

            $checkPaid = SaleReturnPayment::where('sale_return_id', $saleReturn->id)->first();

            if ($checkPaid && $checkPaid->status == 'paid') {
                return response()->json(['message' => 'Already Return Paid'], 200);
            }

            $saleReturnPayment                 = new SaleReturnPayment();
            $saleReturnPayment->created_by     = Auth::user()->id;
            $saleReturnPayment->sale_return_id = $saleReturn->id;
            $saleReturnPayment->customer_id    = $saleReturn->customer_id;
            $saleReturnPayment->note           = $request->note;

            $cash       = $request->cash;
            $saleReturnPayment->cash   += $cash;
            $bank       = $request->bank;
            $saleReturnPayment->bank   += $bank;
            $bkash      = $request->bkash;
            $saleReturnPayment->bkash  += $bkash;
            $nagad      = $request->nagad;
            $saleReturnPayment->nagad  += $nagad;
            $rocket     = $request->rocket;
            $saleReturnPayment->rocket += $rocket;
            $cheque     = $request->cheque;
            $saleReturnPayment->cheque += $cheque;

            $returnPaidAmount = $cash + $bank + $bkash + $nagad + $rocket + $cheque;

            if ($saleReturn->return_amount != $returnPaidAmount ) {
                return response()->json([
                    'sale_return_amount' => $saleReturn->return_amount,
                    'return_paid_amount' => $returnPaidAmount,
                    'message' => 'Pay Full Return Amount!',
                ], 404);
            }

            /*____________________ Account balance ____________________*/

            $balance = Account::where('id', '1')->first();
            if ($balance) {
                $returnedAmount = $saleReturn->return_amount;
                if ($balance->available_balance < $returnedAmount) {
                    return response()->json(['message' => 'Insufficient Account Balance!'], 404);
                }
                $balance->available_balance -= $returnedAmount;
                $balance->save();
            } else {
                return response()->json(['message' => 'Account Eroor!'], 404);
            }

            $saleReturnPayment->return_paid_amount = $returnPaidAmount;
            // Document upload
            $filename = NULL;
            if ($request->hasFile('document')) {
                $document = $request->file('document');
                $filename = time() . uniqid() . "." . $document->extension();
                $location = public_path('document/salereturnpayment');
                $document->move($location, $filename);
                $saleReturnPayment->document = $filename;
            }

            $saleReturnPayment->payment_status = 'paid';
            $saleReturn->payment_status = 'paid';
            $saleReturn->save();
            $saleReturnPayment->save();

            /*____________________ Transaction ____________________*/
            $transaction                     = new Transaction();
            $transaction->date               = $saleReturn->created_at;
            $transaction->branch_id          = $saleReturn->branch_id;
            $transaction->customer_id        = $saleReturn->customer_id;
            $transaction->variation_id       = $saleReturn->variation_id;
            $transaction->sale_id            = $saleReturn->sale_id;
            $transaction->transaction_type   = 'sale-return-paid';
            $transaction->branch_id          = $saleReturn->branch_id;
            $transaction->customer_id        = $saleReturn->customer_id;
            $transaction->transaction_amount = $returnPaidAmount;
            $transaction->payment_status     = 'paid';
            $transaction->save();

            return response()->json(['message' => 'Sale Return Payment successful', 'data' => new SaleReturnPaymentResource($saleReturnPayment)], 200);
        }
        catch(\Exception $e){
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