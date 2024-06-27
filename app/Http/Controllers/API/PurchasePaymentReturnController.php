<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchasePaymentReturnRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchasePaymentReturn;
use App\Models\PurchaseReturn;
use App\Models\Account;
use App\Models\Transaction;
use App\Http\Resources\PurchasePaymentReturnResource;

class PurchasePaymentReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchasePaymentReturn = PurchasePaymentReturn::latest()->get();
        if (!$purchasePaymentReturn) {
            return response()->json(['message' => 'No Purchase Payment Return found!'], 200);
        }
        return PurchasePaymentReturnResource::collection($purchasePaymentReturn);
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
    public function store(PurchasePaymentReturnRequest $request)
    {
        try{
            $purchaseReturn = PurchaseReturn::where('id',$request->purchase_return_id)->first();

            $purchasePaymentReturn = new PurchasePaymentReturn();
            $purchasePaymentReturn->purchase_return_id = $request->purchase_return_id;
            $purchasePaymentReturn->user_id = Auth::id();
            $purchasePaymentReturn->amount = $request->amount;
            if($purchaseReturn->due_amount < $request->amount){
                return response()->json(['beshii takaa diya disen']);
            }
            $purchasePaymentReturn->date = $request->date;
            $purchasePaymentReturn->reference = $request->reference;
            $purchasePaymentReturn->payment_method = $request->payment_method;
            $purchasePaymentReturn->note = $request->note;
            $purchasePaymentReturn->save();

            if($purchasePaymentReturn){
                $account = Account::first();
                $availableBalance = $account->available_balance;
                $account->available_balance = $availableBalance + $request->amount;
                $account->save();

                if($account){
                    $transaction = new Transaction();
                    $transaction->date = $request->date;
                    $transaction->supplier_id = $purchaseReturn->supplier_id;
                    $transaction->purchase_id = $purchaseReturn->purchase_id;
                    $transaction->transaction_type = 'purchase-return-due';
                    $transaction->transaction_amount = $request->amount;
                    if ($purchaseReturn->due_amount == $request->amount) {
                        $transaction->payment_status = 'paid';
                    } else{
                        $transaction->payment_status = 'due';
                    }
                    $transaction->save();
                }

                $purchaseReturn->due_amount -= $request->amount;
                $purchaseReturn->save();
            }

            return response()->json(['message' => 'Purchase Payment Return store successfully', 'data' => new PurchasePaymentReturnResource($purchasePaymentReturn)], 200);
        }
        catch(\Exception $e){
            return response()->json(['message' => 'An error occurred :' . $e->getMessage()],500);
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
        $purchasePaymentReturn = PurchasePaymentReturn::find($id);
        if (!$purchasePaymentReturn) {
            return response()->json(['message' => 'No Purchase Payment Return found!'], 200);
        }

        return new PurchasePaymentReturnResource($purchasePaymentReturn);
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
    public function update(PurchasePaymentReturnRequest $request, $id)
    {
        $purchasePaymentReturn = PurchasePaymentReturn::find($id);
        if (!$purchasePaymentReturn) {
            return response()->json(['message' => 'Purchase Payment Return not found'], 404);
        }
        $purchasePaymentReturn->purchase_return_id = $request->purchase_return_id;
        $purchasePaymentReturn->user_id = Auth::id();
        $purchasePaymentReturn->amount = $request->amount;
        $purchasePaymentReturn->date = $request->date;
        $purchasePaymentReturn->reference = $request->reference;
        $purchasePaymentReturn->payment_method = $request->payment_method;
        $purchasePaymentReturn->note = $request->note;
        $purchasePaymentReturn->update();

        return response()->json([
            'message' => 'Purchase Payment Return updated successfully!',
            'data' => new PurchasePaymentReturnResource($purchasePaymentReturn),
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
        $purchasePaymentReturn = PurchasePaymentReturn::find($id);
        if (!$purchasePaymentReturn) {
            return response()->json([
                'message' => 'Purchase Payment Return not found!'
            ], 404);
        }
        $purchasePaymentReturn->delete();

        return response()->json([
            'message' => 'Purchase Payment Return deleted successfully!',
        ],200);
    }
}
