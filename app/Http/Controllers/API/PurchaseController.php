<?php

namespace App\Http\Controllers\API;

use App\Models\Account;
use App\Models\Purchase;
use App\Models\Variation;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\PurchasePaymentResource;
use App\Models\SupplierTransaction;
use App\Models\SupplierTransactionPayment;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if (Auth::user()->user_type == 2) {
        //     $purchase = Purchase::where('created_by', Auth::user()->id)->latest()->get();
        // } else {
        //     // For admins or other user types, retrieve all sales data
        //     $purchase = Purchase::latest()->get();
        // }

        $purchase = Purchase::latest()->get();

        
        if ($purchase->isEmpty()) {
            return response()->json(['message' => 'No purchase found'], 200);
        }
        return PurchaseResource::collection($purchase);
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
    public function store(PurchaseRequest $request)
    {
        // Account Part
        $account = Account::first();
        $currentBalance = $account->available_balance;
        // if ($currentBalance < $request->paid_amount) {
        //     return response()->json(['message' => 'Account Balance Insufficient!'], 404);
        // }

        $purchase = Purchase::create([
            'supplier_id' => $request->supplier_id,
            'product_id' => $request->product_id,
            'purchase_date' => $request->purchase_date,
            'reference_no' => $request->reference_no,
            'name' => $request->name,
            'variation_id' => json_encode($request->variation_id),
            'quantity' => json_encode($request->quantity),
            'unit_price' => json_encode($request->unit_price),
            'paid_amount' => $request->paid_amount,
            'payment_method' => $request->payment_method,
            'shipping_amount' => $request->shipping_amount,
            'note' => $request->note,
            'purchase_status' => $request->purchase_status,
            'created_by' => Auth::user()->id,
        ]);

        $sub_totals = [];
        $total_amount = 0;

        $total_product = 0;
        foreach ($request->quantity as $key => $quantity) {
            $total_product += $request->quantity[$key];
        }
        $shipping_cost_unit = $request->shipping_amount / $total_product;

        foreach ($request->quantity as $key => $quantity) {
            $sub_total[] = "" . (($request->quantity[$key] * $request->unit_price[$key])) . "";
            $sub_totals += $sub_total;
        }


        $purchase->sub_total = json_encode($sub_totals);
        $purchase->sub_total_amount = array_sum($sub_totals);

        $totalAmount = $purchase->total_amount = $purchase->sub_total_amount + $request->shipping_amount;
        $paidAmount = $request->paid_amount;
        $dueAmount = $purchase->due_amount = $totalAmount - $paidAmount;

        if ($request->hasFile('document')) {
            $image = $request->file('document');
            $filename = time() . uniqid() . "." . $image->extension();
            $location = public_path('document/purchase');
            $image->move($location, $filename);
            $purchase->document = $filename;
        }

        $updatedBalance = $currentBalance - $request->paid_amount;
        $account->available_balance = $updatedBalance;
        $account->save();

        if ($paidAmount == $totalAmount) {
            $purchase->payment_status = 'paid';
        } elseif ($dueAmount == $totalAmount) {
            $purchase->payment_status = 'unpaid';
        } else {
            $purchase->payment_status = 'partial';
        }

        $totalPurchases = Purchase::count();
        $invoiceNumber = str_pad($totalPurchases + 1, 6, '0', STR_PAD_LEFT);
        $purchase->invoice = 'INV-PURCHASE-' . $invoiceNumber;

        //dd($purchase);
        $purchase->save();

        // if ($purchase) {
        //     $supplierId = $purchase->supplier_id;

        //     $existingTransaction = SupplierTransaction::where('supplier_id', $supplierId)->first();

        //     if ($existingTransaction) {
        //         $existingTransaction->total_amount += $purchase->total_amount;
        //         $existingTransaction->paid_amount += $purchase->paid_amount;
        //         $existingTransaction->due_amount += $purchase->due_amount;
        //         $existingTransaction->save();

        //         // if($existingTransaction){
        //         //    $existingTransactionPayment = SupplierTransactionPayment::where('supplier_id', $supplierId)->first();
        //         //    $existingTransactionPayment
        //         // }
        //     } else {
        //         $supplierTransaction = new SupplierTransaction();
        //         $supplierTransaction->supplier_id = $supplierId;
        //         $supplierTransaction->total_amount = $purchase->total_amount;
        //         $supplierTransaction->paid_amount = $purchase->paid_amount;
        //         $supplierTransaction->due_amount = $purchase->due_amount;
        //         $supplierTransaction->save();

        //         // if($supplierTransaction){
        //         //     $stp = new SupplierTransactionPayment();
        //         //     $stp->supplier_transaction_id = $supplierTransaction->id;
        //         //     $stp->supplier_id = $supplierId;
        //         //     $stp->total_amount = $purchase->total_amount;
        //         //     $stp->payment_method = $purchase->payment_method;
        //         //     $stp->paid_amount = $purchase->paid_amount;
        //         //     $stp->due_amount = $purchase->due_amount;
        //         //     $stp->save();
        //         // }
        //     }
        // }

        if ($request->purchase_status == 'receive') {
            foreach ($request->variation_id as $key => $variation) {
                $variation_product = Variation::find($variation);
                if ($variation_product) {
                    $variation_product->default_sell_price = $request->unit_price[$key];
                    $variation_product->stock_amount += $request->quantity[$key];
                    $variation_product->save();
                } else {
                    return response()->json(['message' => 'Variation is not found!'], 404);
                }
            }
        }
        return response()->json([
            'message' => 'Purchase created successfully',
            'data' => new PurchaseResource($purchase),
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
        $purchase = Purchase::find($id);
        $variations = $purchase->productVariation;
        // dd($variations);
        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found!'], 404);
        }
        return new PurchaseResource($purchase);
    }

    public function purchaseDuePayment(Request $request, $id)
    {
        $request->validate([
            'paid_amount' => 'required',
            'payment_method' => 'required',
            'date' => 'required',
        ]);

        try {
            $purchase = Purchase::find($id);
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
            $purchasePayment->purchase_id = $request->id;
            $purchasePayment->created_by = Auth::id();

            $paidAmount = $purchasePayment->paid_amount = $request->paid_amount;

            $purchase = Purchase::where('id', $request->id)->first();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(PurchaseRequest $request, $id)
    // {
    //     $purchase = Purchase::find($id);
    //     $purchase_quantity = json_decode($purchase->quantity);
    //     if($request->purchase_status == 'receive'){
    //         foreach($request->variation_id as $key => $variation){
    //             $variation_product = Variation::find($variation);
    //             $variation_product->stock_amount -= $purchase_quantity[$key];
    //             $variation_product->default_sell_price = $request->unit_price[$key];
    //             $variation_product->stock_amount += $request->quantity[$key];
    //             $variation_product->save();
    //         }
    //     }
    //     $purchase->update([
    //         'supplier_id' => $request->supplier_id,
    //         'product_id' => $request->product_id,
    //         'branch_id' => $request->branch_id,
    //         'purchase_date' => $request->purchase_date,
    //         'reference_no' => $request->reference_no,
    //         'name' => $request->name,
    //         'variation_id' => json_encode($request->variation_id),
    //         'quantity' => json_encode($request->quantity),
    //         'unit_price' => json_encode($request->unit_price),
    //         'tax_percentage' => json_encode($request->tax_percentage),
    //         'discount_percentage' => json_encode($request->discount_percentage),
    //         'paid_amount' => $request->paid_amount,
    //         'payment_method' => $request->payment_method,
    //         'shipping_amount' => $request->shipping_amount,
    //         'note' => $request->note,
    //         'purchase_status' => $request->purchase_status,
    //     ]);
    //     $productTaxs = [];
    //     $discountAmounts = [];
    //     $sub_totals = [];
    //     $total_amount = 0;
    //     $tax_amount = 0;
    //     foreach($request->quantity as $key => $quantity) {
    //        $productTax[] = "".(($request->unit_price[$key]*$request->tax_percentage[$key])/100)."";
    //        $productTaxs += $productTax;
    //        $tax_amount += (($request->quantity[$key]*$request->unit_price[$key]*$request->tax_percentage[$key])/100);
    //        $discountAmount[] = "".(($request->quantity[$key]*$request->unit_price[$key]*$request->discount_percentage[$key])/100)."";
    //        $discountAmounts += $discountAmount;
    //        $sub_total[] = "".(($request->quantity[$key]*$request->unit_price[$key]))."";
    //        $sub_totals += $sub_total;
    //        $total_amount += ($request->quantity[$key]*$request->unit_price[$key])-(($request->quantity[$key]*$request->unit_price[$key]*$request->discount_percentage[$key])/100)+(($request->unit_price[$key]*$request->tax_percentage[$key]*$request->quantity[$key])/100);
    //     }
    //     $purchase->sub_total = json_encode($sub_totals);
    //     $purchase->discount_amount = json_encode($discountAmounts);
    //     $purchase->product_tax_amount = json_encode($productTaxs);
    //     $purchase->tax_amount = $tax_amount;

    //     $totalAmount = $purchase->total_amount = $total_amount + $request->shipping_amount;
    //     $paidAmount = $request->paid_amount;
    //     $dueAmount = $purchase->due_amount = $total_amount - $paidAmount;

    //     if ($request->hasFile('document')) {
    //         $image = $request->file('document');
    //         $filename = time() . uniqid() . "." . $image->extension();
    //         $location = public_path('document/purchase');
    //         $image->move($location, $filename);
    //         $purchase->document = $filename;
    //     }

    //     if($paidAmount == $totalAmount){
    //         $purchase->payment_status = 'paid';
    //     }elseif($dueAmount == $totalAmount){
    //         $purchase->payment_status = 'unpaid';
    //     }else{
    //         $purchase->payment_status = 'partial';
    //     }

    //     $purchase->save();

    //     return response()->json([
    //         'message' => 'Purchase updated successfully',
    //         'data' => new PurchaseResource($purchase),
    //     ],200);
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase = Purchase::find($id);
        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }
        $purchase->delete();
        return response()->json([
            'message' => 'Purchase deleted successfully',
        ], 200);
    }
}