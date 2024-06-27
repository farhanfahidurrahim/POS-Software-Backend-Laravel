<?php

namespace App\Http\Controllers\API;

use App\Models\Account;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchasePaymentRequest;
use App\Http\Resources\PurchasePaymentResource;

class PurchasePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if (Auth::user()->user_type == 2) {
        //     $purchasePayment = PurchasePayment::where('created_by', Auth::user()->id)->latest()->get();
        // } else {
        //     // For admins or other user types, retrieve all sales data
        //     $purchasePayment = PurchasePayment::latest()->get();
        // }
       
        $purchasePayment = PurchasePayment::latest()->get();
        
        if (!$purchasePayment) {
            return response()->json(['message' => 'No Purchase Payment found'], 200);
        }
        return PurchasePaymentResource::collection($purchasePayment);
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
    public function store(PurchasePaymentRequest $request)
    {
        try {
            $purchase = Purchase::find($request->purchase_id);
            if (!$purchase) {
                return response()->json(['message' => 'Purchase Not Found!'], 404);
            }

            // Account Part 1
            $account = Account::first();
            $currentBalance = $account->available_balance;

            if ($currentBalance < $request->paid_amount) {
                return response()->json(['message' => 'Account Insufficient!'], 404);
            }

            $purchasePayment = new PurchasePayment();
            $purchasePayment->purchase_id = $request->purchase_id;
            $purchasePayment->created_by = Auth::id();

            $paidAmount = $purchasePayment->paid_amount = $request->paid_amount;

            $purchase = Purchase::where('id', $request->purchase_id)->first();
            $dueAmount = $purchase->due_amount;
            if ($dueAmount < $request->paid_amount) {
                return response()->json(['message' => 'Apni beshi daam disen!!'], 404);
            }

            $purchasePayment->date = $request->date;
            $purchasePayment->reference = $request->reference;
            if ($paidAmount == $dueAmount) {
                $purchasePayment->payment_status = 'paid';
            } else {
                $purchasePayment->payment_status = 'partial';
            }
            $purchasePayment->payment_method = $request->payment_method;
            $purchasePayment->note = $request->note;
            $purchasePayment->save();

            if ($purchasePayment) {
                // Account Part 2
                $updatedBalance = $currentBalance - $request->paid_amount;
                $account->available_balance = $updatedBalance;
                $account->save();

                if ($account) {
                    // Transaction Part
                    $transaction                     = new Transaction();
                    $transaction->date               = $request->date;
                    $transaction->supplier_id        = $purchase->supplier_id;
                    $transaction->variation_id       = $purchase->variation_id;
                    $transaction->purchase_id        = $purchase->id;
                    $transaction->transaction_type   = 'purchase-due';
                    if ($purchase->due_amount == $purchasePayment->paid_amount) {
                        $transaction->payment_status = 'paid';
                    } else {
                        $transaction->payment_status = 'due';
                    }
                    $transaction->transaction_amount = $paidAmount;
                    $transaction->save();
                }

                $purchase['due_amount'] = $purchase->due_amount - $purchasePayment->paid_amount;
                $purchase['paid_amount'] = $purchase->paid_amount + $purchasePayment->paid_amount;
                if ($purchase->paid_amount == $purchase->total_amount) {
                    $purchase->payment_status = 'paid';
                } else {
                    $purchase->payment_status = 'partial';
                }
                $purchase->save();
            }

            return response()->json(['message' => 'Purchase Payment store successfully', 'data' => new PurchasePaymentResource($purchasePayment)], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred :' . $e->getMessage()], 500);
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
        $purchasePayment = PurchasePayment::find($id);
        if (!$purchasePayment) {
            return response()->json(['message' => 'No Purchase Payment found!'], 200);
        }

        return new PurchasePaymentResource($purchasePayment);
    }

    public function update(PurchasePaymentRequest $request, $id)
    {
        $purchasePayment = PurchasePayment::find($id);
        if (!$purchasePayment) {
            return response()->json(['message' => 'Purchase Payment not found'], 404);
        }
        $purchasePayment->purchase_id = $request->purchase_id;
        $purchasePayment->user_id = Auth::id();
        $purchasePayment->amount = $request->amount;
        $purchasePayment->date = $request->date;
        $purchasePayment->reference = $request->reference;
        $purchasePayment->payment_method = $request->payment_method;
        $purchasePayment->note = $request->note;
        $purchasePayment->update();

        return response()->json([
            'message' => 'Purchase Payment updated successfully!',
            'data' => new PurchasePaymentResource($purchasePayment),
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
        $purchasePayment = PurchasePayment::find($id);
        if (!$purchasePayment) {
            return response()->json([
                'message' => 'Purchase Payment not found!'
            ], 404);
        }
        $purchasePayment->delete();

        return response()->json([
            'message' => 'Purchase Payment deleted successfully!',
        ], 200);
    }
}