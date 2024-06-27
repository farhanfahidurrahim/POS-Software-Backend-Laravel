<?php

namespace App\Http\Controllers\API;

use App\Models\Quotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QuotationRequest;
use App\Http\Resources\QuotationResource;
use App\Http\Resources\Quotation as ResourcesQuotation;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotations = Quotation::latest()->get();
        if ($quotations->isEmpty()) {
            return response()->json(['message' => 'No Quotation found!'], 200);
        }
        return QuotationResource::collection($quotations);
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
    public function store(QuotationRequest $request)
    {
        try {
            $quotation = new Quotation();
            $quotation->created_by = Auth::user()->id;
            $quotation->customer_id = $request->customer_id;
            $quotation->branch_id = $request->branch_id;

            $quotation->variation_id = json_encode($request->variation_id);
            $quotation->unit_price = json_encode($request->unit_price);
            $quotation->quantity = json_encode($request->quantity);

            $quotation->discount_percentage = json_encode($request->discount_percentage);
            $quotation->discount_amount = json_encode($request->discount_amount);

            $quotation->sub_totals = $request->sub_totals;

            $quotation->vat_percentage = $request->vat_percentage;
            $quotation->vat_amount = $request->vat_amount;

            $quotation->discount_type_subtotal = $request->discount_type_subtotal;
            $quotation->discount_on_subtotal = $request->discount_on_subtotal;
            $quotation->discount_on_subtotal_amount = $request->discount_on_subtotal_amount;

            $quotation->shipping_amount = $request->shipping_amount;
            $quotation->total_amount = $request->total_amount;

            $quotation->status = $request->status;
            $quotation->note = $request->note;

            $totalQuotation = Quotation::count();
            $invoiceNumber = str_pad($totalQuotation + 1, 6, '0', STR_PAD_LEFT);
            $quotation->invoice = 'INV-QUOTATION-' . $invoiceNumber;

            $quotation->save();

            return response()->json(['message' => 'Quotation store successfully', 'data' => new QuotationResource($quotation)], 200);
        } catch (\Exception $e) {
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
        $quotation = Quotation::find($id);
        if (!$quotation) {
            return response()->json(['message' => 'Quotation not found'], 404);
        }
        return new QuotationResource($quotation);
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
    public function update(QuotationRequest $request, $id)
    {
        // try{
        //     $quotation = Quotation::find($id);
        //     if (!$quotation) {
        //         return response()->json(['message' => 'Quotation not found'], 404);
        //     }

        //     $quotation->date = $request->date;
        //     $quotation->reference = $request->reference;
        //     $quotation->customer_id = $request->customer_id;
        //     $quotation->user_id = Auth::user()->id;
        //     $quotation->branch_id = $request->branch_id;
        //     $quotation->product_variation_id = $request->product_variation_id;

        //     $unitPrice = $quotation->unit_price = $quotation->productVariation->sell_price_inc_tax;
        //     $quantity = $quotation->quantity = $request->quantity;

        //     $totalPrice = $unitPrice * $quantity;

        //     $taxPercentage = $quotation->tax_percentage = $request->tax_percentage;
        //     $discountTax = ($totalPrice * $taxPercentage)/100;
        //     $afterTax_Price = $totalPrice + $discountTax;
        //     $quotation->tax_amount = number_format($discountTax, 2, '.', '');

        //     $discountPercentage = $quotation->discount_percentage = $request->discount_percentage;
        //     $discountAmount = ($afterTax_Price * $discountPercentage)/100;
        //     $afterDiscount_Price = $afterTax_Price - $discountAmount;
        //     $quotation->discount_amount = number_format($discountAmount, 2, '.', '');

        //     $quotation->shipping_amount = $request->shipping_amount;
        //     $totalAmount = ($afterDiscount_Price + $request->shipping_amount);
        //     $quotation->total_amount = number_format($totalAmount, 2, '.', '');

        //     $quotation->status = $request->status;
        //     $quotation->note = $request->note;
        //     $quotation->update();

        //  return response()->json(['message' => 'Quotation updated successfully', 'data' => new QuotationResource($quotation)], 200);
        // }
        // catch(\Exception $e){
        //     return response()->json(['message' => 'An error occured: ' . $e->getMessage()], 500);
        // }   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) {
            return response()->json(['message' => 'Quotation not found'], 404);
        }
        $quotation->delete();

        return response()->json(['message' => 'Quotation data deleted successfully'], 200);
    }
}
