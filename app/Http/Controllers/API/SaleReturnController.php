<?php

namespace App\Http\Controllers\API;

use App\Models\Sale;
use App\Models\Branch;
use App\Models\Variation;
use App\Models\SaleReturn;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\TransferStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SaleReturnRequest;
use App\Http\Resources\SaleReturnResource;

class SaleReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if (Auth::user()->user_type == 2) {
        //     $saleReturn = SaleReturn::where('created_by', Auth::user()->id)->latest()->get();
        // } else {
        //     // For admins or other user types, retrieve all sales data
        //     $saleReturn = SaleReturn::latest()->get();
        // }

        $saleReturn = SaleReturn::latest()->get();
       
        if ($saleReturn->isEmpty()) {
            return response()->json(['message' => 'No Sale Return found'], 200);
        }
        return SaleReturnResource::collection($saleReturn);
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
            'return_quantity' => 'required',
        ]);

        try {
            $sale = Sale::find($request->sale_id);
            if (!$sale) {
                return response()->json(['message' => 'No Sale Found. Create Sale First!'], 404);
            } elseif ($sale->id != $request->sale_id) {
                return response()->json(['message' => 'Incorrect Sale ID!'], 404);
            }

            $saleReturn                  = new SaleReturn();
            $saleReturn->created_by      = Auth::user()->id;
            $saleReturn->sale_id         = $request->sale_id;
            $saleReturn->branch_id       = $sale->branch_id;
            $saleReturn->customer_id     = $sale->customer_id;
            $saleReturn->variation_id    = json_encode($request->variation_id);
            $saleReturn->unit_price      = json_encode($request->unit_price);
            $saleReturn->return_quantity = json_encode($request->return_quantity);
            $saleReturn->note            = $request->note;

            $unit_prices      = [];
            $return_quantitys = [];
            $totalPrice       = 0;

            foreach ($request->return_quantity as $key => $quantity) {
                $unit_prices[] = isset($request->unit_price[$key]) ? $request->unit_price[$key] : 0;

                $withDiscountPrice = $request->unit_price[$key] ?? 0;
                $sub_total[]       = $quantity * $withDiscountPrice;
                $totalPrice        += $unit_prices[$key] * $request->return_quantity[$key];
                $return_quantitys[] = $request->return_quantity[$key];
            }

            $saleReturn->discount_amount = json_encode($request->discount_amount);
            $subTotal = json_encode($sub_total);
            $saleReturn->sub_totals = $request->sub_totals;
            $saleReturn->discount_on_subtotal_amount = $request->discount_on_subtotal_amount;


            // Sale Update Quantity
            $quantityArray = json_decode($sale->quantity, true);
            //dd($quantityArray);
            $newQuantityArray = [];
            // dd($newQuantityArray);

            foreach ($quantityArray as $key => $quantity) {
                if (isset($return_quantitys[$key])) {
                    $newQuantityArray[$key] = $quantity - $return_quantitys[$key];
                } else {
                    $newQuantityArray[$key] = $quantity;
                }
            }

            $sale->quantity = json_encode($newQuantityArray);
            $sale->sale_return = $return_quantitys;
            // dd($sale->total_amount);
            $sale->total_amount -= $request->return_amount;
            $sale->due_amount = $sale->total_amount;
            $sale->save();

            $saleReturn->unit_price = json_encode($unit_prices);
            $saleReturn->return_amount       = $request->return_amount;
            $saleReturn->return_quantity     = json_encode($return_quantitys);

            //Manage Payment Status
            $quantityArray = json_decode($sale->quantity, true);
            if (is_array($quantityArray) && count($quantityArray) > 0 && array_sum($quantityArray) === 0) {
                $saleReturn->payment_status = 'Full Return';
            } else {
                $saleReturn->payment_status = 'Partial Return';
            }

            foreach ($request->variation_id as $key => $variation) {
                $stockVariation    = Variation::find($variation);
                $getReturnQuantity = $request->return_quantity[$key];
                $currentQuantity   = $stockVariation->stock_amount;
                $newQuantity       = $currentQuantity + $getReturnQuantity;
                $stockVariation->stock_amount = max(0, $newQuantity);
                $stockVariation->save();
            }

            // Document upload and save
            $filename = NULL;
            if ($request->hasFile('document')) {
                $document = $request->file('document');
                $filename = time() . uniqid() . "." . $document->extension();
                $location = public_path('document/salereturn');
                $document->move($location, $filename);
                $saleReturn->document = $filename;
            }

            $saleReturn->save();

            return response()->json(['message' => 'Sale Return store successfully', 'data' => new SaleReturnResource($saleReturn)], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('An error occurred: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());

            // Return a more generic error message to the user
            return response()->json([
                'message' => 'An error occurred. Please try again.',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
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
        $saleReturn = SaleReturn::find($id);
        if (!$saleReturn) {
            return response()->json(['message' => 'Sale not found'], 404);
        }
        return new SaleReturnResource($saleReturn);
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $saleReturn = SaleReturn::find($id);
        if (!$saleReturn) {
            return response()->json(['message' => 'Sale Return not found'], 404);
        }
        $saleReturn->delete();

        return response()->json(['message' => 'Sale Return data deleted successfully'], 200);
    }
}