<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchageReturnRequest;
use App\Http\Resources\PurchaseReturnResource;
use App\Models\Account;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Transaction;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Auth;
class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchase_returns = PurchaseReturn::latest()->get();
        if($purchase_returns->isEmpty()){
            return response()->json(['message' => 'No purchase return found'], 200);
        }
        return PurchaseReturnResource::collection($purchase_returns);
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
    public function store(PurchageReturnRequest $request)
    {
        try{
            $purchase = Purchase::find($request->purchase_id);
            if(!$purchase){
                return response()->json([
                    'message' => 'Purchase Not found',
                ],200);
            }

            $purchase_return = PurchaseReturn::create([
                "purchase_id" => $request->purchase_id ,
                "branch_id" => $request->branch_id,
                "supplier_id" => $request->supplier_id,
                "reference" => $request->reference,
                "date" => $request->date,
                "user_id" => Auth::user()->id,
                "status" => $request->status,
                "payment_status" => $request->payment_status,
                "payment_method" => $request->payment_method,
                "note" => $request->note,
            ]);
            $unit_price_with_alls = [];
            $return_quantitys = [];
            $totalprice = 0;
            $variations = [];
            foreach($request->variation_id as $key => $variation_id){
                $product_variation = Variation::find($request->variation_id[$key]);
                $product_variation->stock_ammount = $product_variation->stock_ammount-$request->return_quantity[$key];
                $product_variation->save();
                $variation[] = "". $request->variation_id[$key] ."";
                $variations += $variation;
                $unit_price_with_all[] = "". $request->unit_price_with_all[$key] ."";
                $unit_price_with_alls += $unit_price_with_all;
                $totalprice += $unit_price_with_all[$key] * $request->return_quantity[$key] ;
                $return_quantity[] = "". $request->return_quantity[$key] ."";
                $return_quantitys += $return_quantity;
            }
            
            $purchase->return_purchase = $return_quantitys;
            $purchase->save();
            $purchase_return->variation_id = json_encode($variations);
            $purchase_return->total_amount = $totalprice;
            $purchase_return->paid_amount = $request->paid_amount; //paid amount means get amount from supplier
            $purchase_return->due_amount = $totalprice-$request->paid_amount;
            $purchase_return->unit_price_with_all = json_encode($unit_price_with_alls);
            $purchase_return->return_quantity = json_encode($return_quantitys);
            $purchase_return->save();

            if($request->paid_amount){
                $account = Account::first();
                $availableBalance = $account->available_balance;
                $account->available_balance = $availableBalance + $request->paid_amount;
                $account->save();

                if($account){
                    $transaction = new Transaction();
                    $transaction->date = $request->date;
                    $transaction->supplier_id = $purchase->supplier_id;
                    $transaction->purchase_id = $purchase->id;
                    $transaction->transaction_type = 'purchase-return';
                    $transaction->transaction_amount = $request->paid_amount;
                    $transaction->payment_status = $request->payment_status;
                    $transaction->save();
                }
            }

            return response()->json([
                'message' => 'Purchase return created successfully',
                'data' => new PurchaseReturnResource($purchase_return),
            ],200);
        }

        catch (\Exception $e) {
            // Handle the exception here
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
        $purchase_return = PurchaseReturn::find($id);
        if (!$purchase_return) {
            return response()->json(['message' => 'Purchase return not found'], 404);
        }
        return new PurchaseReturnResource($purchase_return);
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
    public function update(PurchageReturnRequest $request, $id)
    {

        try{
            $purchase_return = PurchaseReturn::find($id);
            if (!$purchase_return) {
                return response()->json(['message' => 'Purchase return not found'], 404);
            }
            $return_quantity_ok = json_decode($purchase_return->return_quantity);
            foreach( $request->variation_id  as $key=>$variation_id){
                   $variation_maintain = Variation::find($variation_id);
                   $variation_maintain->stock_ammount += $return_quantity_ok[$key];
                   $variation_maintain->update();
            }
            $purchase_return->update([
                "purchase_id" => $request->purchase_id ,
                "branch_id" => $request->branch_id,
                "supplier_id" => $request->supplier_id,
                "reference" => $request->reference,
                "date" => $request->date,
                "user_id" => Auth::user()->id,
                "status" => $request->status,
                "payment_status" => $request->payment_status,
                "payment_method" => $request->payment_method,
                "note" => $request->note,
            ]);
            $unit_price_with_alls = [];
            $return_quantitys = [] ;
            $totalprice = 0;
            $variations = [];
            foreach($request->variation_id as $key => $variation_id){
                $product_variation = Variation::find($request->variation_id[$key]);
                $product_variation->stock_ammount = $product_variation->stock_ammount-$request->return_quantity[$key];
                $product_variation->save();
                $variation[] = "". $request->variation_id[$key] ."";
                $variations += $variation;
                $unit_price_with_all[] = "". $request->unit_price_with_all[$key] ."";
                $unit_price_with_alls += $unit_price_with_all;
                $totalprice += $unit_price_with_all[$key] * $request->return_quantity[$key] ;
                $return_quantity[] = "". $request->return_quantity[$key] ."";
                $return_quantitys += $return_quantity;
            }
            $purchase = Purchase::find($request->purchase_id);
            if(!$purchase){
                return response()->json([
                    'message' => 'Purchase Not found',
                ],200);
            }
            $purchase->return_purchase = $return_quantitys;
            $purchase->save();
            $purchase_return->variation_id = json_encode($variations);
            $purchase_return->total_amount = $totalprice;
            $purchase_return->paid_amount = $request->paid_amount;
            $purchase_return->due_amount = $totalprice-$request->paid_amount;
            $purchase_return->unit_price_with_all = json_encode($unit_price_with_alls);
            $purchase_return->return_quantity = json_encode($return_quantitys);
            $purchase_return->save();
            return response()->json([
                'message' => 'Purchase return updated successfully',
                'data' => new PurchaseReturnResource($purchase_return),
            ],200);
        }
        catch (\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase_return = PurchaseReturn::find($id);
        if (!$purchase_return) {
            return response()->json(['message' => 'Purchase return not found'], 404);
        }
        $purchase_return->delete();
        return response()->json([
            'message' => 'Purchase return deleted successfully',
        ],200);
    }
}
