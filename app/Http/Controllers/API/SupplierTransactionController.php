<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\SupplierTransaction;
use App\Http\Controllers\Controller;
use App\Models\SupplierTransactionPayment;
use App\Http\Resources\SupplierTransactionResource;
use App\Http\Resources\SupplierTransactionPaymentResource;

class SupplierTransactionController extends Controller
{
    public function supplierTransaction()
    {
        $supplierTransaction = SupplierTransaction::latest()->get();
        return response()->json([
            'data' => SupplierTransactionResource::collection($supplierTransaction),
        ], 200);
    }

    public function supplierTransactionStore(Request $request)
    {
        $request->validate([
            'date' => 'date|required',
            'supplier_id' => 'required',
            'total_amount' => 'required',
            'payment_method' => 'required',
            'paid_amount' => 'required',
            'document' => 'nullable|max:10000|mimes:doc,docx,pdf,xlsx,jpg,jpeg,png,webp',
        ]);

        $supplierId = $request->supplier_id;

        $existingTransaction = SupplierTransaction::where('supplier_id', $supplierId)->first();

        if ($existingTransaction) {
            $existingTransaction->total_amount += $request->total_amount;
            $existingTransaction->paid_amount += $request->paid_amount;
            $existingTransaction->due_amount += $request->total_amount - $request->paid_amount;
            $existingTransaction->note = $request->note;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . uniqid() . "." . $file->extension();
                $location = public_path('document/suppliertransaction');
                $file->move($location, $fileName);
                $existingTransaction->document = $fileName;
            }
            $existingTransaction->save();
            if ($existingTransaction) {
                $stp = new SupplierTransactionPayment();
                $stp->supplier_transaction_id = $existingTransaction->id;
                $stp->date = $request->date;
                $stp->supplier_id = $supplierId;
                $stp->total_amount = $request->total_amount;
                $stp->payment_method = $existingTransaction->payment_method;
                $stp->paid_amount = $request->paid_amount;
                $stp->due_amount = $request->total_amount - $request->paid_amount;
                $stp->note = $request->note;
                $stp->document = $existingTransaction->document;
                $stp->save();
            }
        } else {
            $supplierTransaction = new SupplierTransaction();
            $supplierTransaction->supplier_id = $request->supplier_id;
            $supplierTransaction->total_amount = $request->total_amount;
            $supplierTransaction->payment_method = $request->payment_method;
            $supplierTransaction->paid_amount = $request->paid_amount;
            $supplierTransaction->due_amount = $request->total_amount - $request->paid_amount;
            $supplierTransaction->note = $request->note;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . uniqid() . "." . $file->extension();
                $location = public_path('document/suppliertransaction');
                $file->move($location, $fileName);
                $supplierTransaction->document = $fileName;
            }
            $supplierTransaction->save();
            if ($supplierTransaction) {
                $stp = new SupplierTransactionPayment();
                $stp->supplier_transaction_id = $supplierTransaction->id;
                $stp->date = $request->date;
                $stp->supplier_id = $supplierId;
                $stp->total_amount = $supplierTransaction->total_amount;
                $stp->payment_method = $supplierTransaction->payment_method;
                $stp->paid_amount = $supplierTransaction->paid_amount;
                $stp->due_amount = $supplierTransaction->due_amount;
                $stp->note = $supplierTransaction->note;
                $stp->document = $supplierTransaction->document;
                $stp->save();
            }

            $existingTransaction = $supplierTransaction;
        }

        return response()->json([
            'message' => "Supplier Transaction Created!",
            'data' => new SupplierTransactionResource($existingTransaction),
        ], 200);
    }

    public function supplierTransactionShow($id)
    {
        $supplierTransaction = SupplierTransaction::find($id);

        return response()->json([
            'data' => new SupplierTransactionResource($supplierTransaction),
        ], 200);
    }

    public function supplierTransactionDuePayment(Request $request, $st_id)
    {
        $request->validate([
            'payment_method' => 'required',
            'paid_amount' => 'required',
            'date' => 'required',
            'document' => 'nullable|max:10000|mimes:doc,docx,pdf,xlsx,jpg,jpeg,png,webp',
        ]);

        $supplierTransaction = SupplierTransaction::find($st_id);
        if (!$supplierTransaction) {
            return response()->json([
                'message' => "Supplier Transaction not found!",
            ], 404);
        }

        $supplierTransaction->paid_amount += $request->paid_amount;
        $supplierTransaction->due_amount -= $request->paid_amount;
        $supplierTransaction->save();

        if ($supplierTransaction) {

            $SupplierTransaction_Payment = new SupplierTransactionPayment();
            $SupplierTransaction_Payment->date = $request->date;
            $SupplierTransaction_Payment->supplier_transaction_id = $supplierTransaction->id;
            $SupplierTransaction_Payment->supplier_id = $supplierTransaction->supplier_id;
            $SupplierTransaction_Payment->total_amount = 0;
            $SupplierTransaction_Payment->payment_method = $request->payment_method;
            $SupplierTransaction_Payment->paid_amount = $request->paid_amount;
            $SupplierTransaction_Payment->due_amount = $supplierTransaction->due_amount;
            $SupplierTransaction_Payment->note = $request->note;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . uniqid() . "." . $file->extension();
                $location = public_path('document/suppliertransaction');
                $file->move($location, $fileName);
                $SupplierTransaction_Payment->document = $fileName;
            }
            $SupplierTransaction_Payment->save();
        }

        return response()->json([
            'message' => "Supplier Due Payment Success!",
            'data' => new SupplierTransactionPaymentResource($SupplierTransaction_Payment),
        ], 200);
    }

    public function supplierTransactionAllPayment($supplier_id)
    {
        $supplierTransactionPayment = SupplierTransactionPayment::where('supplier_id', $supplier_id)->get();

        return response()->json([
            'data' => SupplierTransactionPaymentResource::collection($supplierTransactionPayment),
        ], 200);
    }

    public function supplierTransactionPaymentShow($stp_id)
    {
        $supplierTransactionPayment = SupplierTransactionPayment::find($stp_id);
        if (!$supplierTransactionPayment) {
            return response()->json([
                'message' => "Supplier Transaction Payment id not found!",
            ], 404);
        }

        $previousDueAmount = SupplierTransactionPayment::where('supplier_id', $supplierTransactionPayment->supplier_id)
            ->where('id', '<', $stp_id)->orderBy('id', 'desc')->select('due_amount')->first();

        $data = (new SupplierTransactionPaymentResource($supplierTransactionPayment))->toArray(request());
        $data['previous_due_amount'] = $previousDueAmount;

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function supplierTransactionPaymentUpdate(Request $request, $stp_id)
    {
        //return $request->all();
        $request->validate([
            'payment_method' => 'required',
            'paid_amount' => 'required',
            'date' => 'required',
            'document' => 'nullable|max:10000|mimes:doc,docx,pdf,xlsx,jpg,jpeg,png,webp',
        ]);

        $supplierTransactionPayment = SupplierTransactionPayment::find($stp_id);
        if (!$supplierTransactionPayment) {
            return response()->json([
                'message' => "Supplier Transaction Payment id not found!",
            ], 404);
        }

        $supplierTransactionPayment->paid_amount = $request->paid_amount;
        $dueAmount = $supplierTransactionPayment->total_amount - $request->paid_amount;
        $supplierTransactionPayment->due_amount = $dueAmount;
        $supplierTransactionPayment->note = $request->note;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = time() . uniqid() . "." . $file->extension();
            $location = public_path('document/suppliertransaction');
            $file->move($location, $fileName);
            $supplierTransactionPayment->document = $fileName;
        }
        $supplierTransactionPayment->save();

        return response()->json([
            'message' => "Supplier Transaction Payment updated successfully!",
            'data' => $supplierTransactionPayment,
        ], 200);
    }

    public function supplierTransactionPaymentDelete($stp_id)
    {
        $supplierTransactionPayment = SupplierTransactionPayment::find($stp_id);

        if (!$supplierTransactionPayment) {
            return response()->json([
                'message' => "Supplier Transaction Payment id not found!",
            ], 404);
        }

        try {

            $paidAmount = $supplierTransactionPayment->paid_amount;

            $st = SupplierTransaction::where('supplier_id', $supplierTransactionPayment->supplier_id)->first();
            $st->paid_amount -= $paidAmount;
            $st->due_amount = $st->total_amount - $st->paid_amount;
            $st->save();

            $supplierTransactionPayment->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Failed to delete Supplier Transaction Payment!",
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => "Supplier Transaction Payment deleted successfully!",
        ], 200);
    }

    /////////////////////////////////////////////////////////////

    // public function supplierTransaction()
    // {
    //     $purchases = Purchase::all();

    //     $totals = [];

    //     foreach ($purchases as $purchase) {
    //         $supplier_id = $purchase->supplier_id;
    //         if (!isset($totals[$supplier_id])) {
    //             $supplier = Supplier::find($supplier_id);
    //             $totals[$supplier_id] = [
    //                 'supplier_id' => [
    //                     'id' => $supplier->id,
    //                     'name' => $supplier->name,
    //                     'phone_number' => $supplier->phone_number,
    //                 ],
    //                 'total_amount' => 0,
    //                 'paid_amount' => 0,
    //                 'due_amount' => 0,
    //             ];
    //         }

    //         $totals[$supplier_id]['total_amount'] += $purchase->total_amount;
    //         $totals[$supplier_id]['paid_amount'] += $purchase->paid_amount;
    //         $totals[$supplier_id]['due_amount'] += $purchase->due_amount;
    //     }
    //     ksort($totals);

    //     return response()->json([
    //         'data' => array_values($totals),
    //     ], 200);
    // }


    // public function supplierTransaction()
    // {
    //     $purchases = Purchase::all();

    //     $totals = [];

    //     foreach ($purchases as $purchase) {
    //         $supplier_id = $purchase->supplier_id;
    //         if (!isset($totals[$supplier_id])) {
    //             $totals[$supplier_id] = [
    //                 'supplier_id' => $supplier_id,
    //                 'total_amount' => 0,
    //                 'paid_amount' => 0,
    //                 'due_amount' => 0,
    //             ];
    //         }

    //         $totals[$supplier_id]['total_amount'] += $purchase->total_amount;
    //         $totals[$supplier_id]['paid_amount'] += $purchase->paid_amount;
    //         $totals[$supplier_id]['due_amount'] += $purchase->due_amount;
    //     }
    //     ksort($totals);

    //     return response()->json([
    //         'data' => SupplierTransactionResource::collection($totals),
    //     ], 200);
    // }



    // public function supplierTransaction()
    // {
    //     $purchases = Purchase::all();

    //     $totals = [];

    //     foreach ($purchases as $purchase) {
    //         $supplier_id = $purchase->supplier_id;
    //         if (!isset($totals[$supplier_id])) {
    //             $totals[$supplier_id] = [
    //                 'total_amount' => 0,
    //                 'paid_amount' => 0,
    //                 'due_amount' => 0,
    //             ];
    //         }

    //         $totals[$supplier_id]['total_amount'] += $purchase->total_amount;
    //         $totals[$supplier_id]['paid_amount'] += $purchase->paid_amount;
    //         $totals[$supplier_id]['due_amount'] += $purchase->due_amount;
    //     }
    //     ksort($totals);

    //     return response()->json([
    //         'totals' => SupplierTransactionResource::collection($totals)
    //     ], 200);
    // }

    // $supplier = Supplier::find($supplier_id);
    // if ($supplier) {
    //     $totals[$supplier_id] = [
    //         'supplier_details' => $supplier,
    //         'total_amount' => 0,
    //         'paid_amount' => 0,
    //         'due_amount' => 0,
    //     ];
    // }
}
