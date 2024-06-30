<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Variation;
use Illuminate\Http\Request;
use App\Models\SaleReschedule;
use App\Http\Requests\SaleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\SaleResource;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Http\Resources\SaleSingleResource;
use App\Http\Resources\SaleRescheduleResource;
use App\Http\Controllers\API\PathaoCourierApiController;
use App\Http\Resources\SaleIndexResource;
use Illuminate\Pagination\LengthAwarePaginator;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Retrieve all sales
        $allSales = Sale::latest()->get();

        // Filter the collection
        $filteredSales = $allSales->filter(function ($sale) {
            $quantityArray = json_decode($sale->quantity, true);
            return is_array($quantityArray) && count($quantityArray) > 0 && array_sum($quantityArray) != 0;
        });

        if ($filteredSales->isEmpty()) {
            return response()->json(['message' => 'No Sale found'], 200);
        }

        // Manually paginate the filtered collection
        $salesForCurrentPage = $filteredSales->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $filteredPaginatedSales = new LengthAwarePaginator(
            $salesForCurrentPage,
            $filteredSales->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Calculate totals
        $totalAmountSales = Sale::where('status', '!=', 'Cancel')->sum('total_amount');
        $totalPaidSales = Sale::where('status', '!=', 'Cancel')->sum('paid_amount');
        $totalDueSales = Sale::where('status', '!=', 'Cancel')->sum('due_amount');

        // $totals = Sale::selectRaw('SUM(total_amount) as total_amount, SUM(paid_amount) as paid_amount, SUM(due_amount) as due_amount')
        //     ->where('status', '!=', 'Cancel')
        //     ->first();

        $totalQuantitySales = $filteredSales->reduce(function ($carry, $sale) {
            $quantityArray = json_decode($sale->quantity, true);
            if (is_array($quantityArray)) {
                return $carry + array_sum($quantityArray);
            }
            return $carry;
        }, 0);

        $totalCancelSale = $allSales->where('status', 'Cancel')->count();

        return SaleIndexResource::collection($filteredPaginatedSales)
            ->additional([
                'total_sale_quantity' => $totalQuantitySales,

                'total_amount' => $totalAmountSales,
                'paid_amount' => $totalPaidSales,
                'due_amount' => $totalDueSales,

                // 'total_amount' => $totals->total_amount,
                // 'paid_amount' => $totals->paid_amount,
                // 'due_amount' => $totals->due_amount,

                'totalCancelSale' => $totalCancelSale
            ]);
    }

    public function indexHeaderTotal()
    {
        try {
            $totals = Sale::selectRaw('SUM(total_amount) as total_amount, SUM(paid_amount) as paid_amount, SUM(due_amount) as due_amount')
                ->where('status', '!=', 'Cancel')
                ->first();

            if (!$totals) {
                throw new \Exception('Failed to retrieve totals.');
            }

            return response()->json([
                'total_amount' => $totals->total_amount,
                'paid_amount' => $totals->paid_amount,
                'due_amount' => $totals->due_amount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Sale Dispatch =>
     */

    public function saleDispatchList()
    {
        $sales = Sale::with(['getCourier', 'getCustomer'])->latest()->paginate(20);
        $saleList = [];

        foreach ($sales as $sale) {
            $dispatchDate = $sale->dispatch_date ? $sale->dispatch_date : '-';
            $saleList[] = [
                'id' => $sale->id,
                'dispatch_date' => $dispatchDate,
                'invoice' => $sale->invoice,
                'courier' => $sale->getCourier->name ?? '-',
                'name' => $sale->getCustomer->name ?? '-',
                'phone_number' => $sale->getCustomer->phone_number ?? '-',
                'location' => $sale->getCustomer->location ?? '-',
                'total_amount' => $sale->total_amount,
                'status' => $sale->status,
                'dispatch_status' => $sale->dispatch_status ? 'Yes' : 'No',
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Sales Dispatch List Retrieved',
            'dispatch' => [
                'data' => $saleList,
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'per_page' => $sales->perPage(),
                'total' => $sales->total(),
            ],
        ]);
    }


    public function saleDispatchStatusChange(Request $request, $id)
    {
        $request->validate([
            'dispatch_status' => 'required|in:0,1',
        ]);

        $sale = Sale::find($id);
        if (!$sale) {
            return response()->json([
                'message' => "Sale id not found!",
            ]);
        }

        if ($request->dispatch_status == 1) {
            $sale->status = 'Dispatched';
            $sale->dispatch_status = 1;
            $sale->dispatch_date = Carbon::now()->format('d-M-Y');
        } else {
            $sale->status = 'Processing';
            $sale->dispatch_status = 0;
            $sale->dispatch_date = null;
        }

        $sale->save();

        return response()->json([
            'status' => 'success',
            'message' => "Dispatch Updated",
            'dispatch_date' => $sale->dispatch_date,
            'dispatch_status' => $sale->dispatch_status,

        ], 200);
    }

    public function barcodeScan($invoice_number)
    {
        $sale = Sale::where('invoice', $invoice_number)->first();
        $sale->dispatch_status = 1;
        $sale->dispatch_date = Carbon::now()->format('d-M-Y');
        $sale->status = "Dispatched";
        $sale->update();

        if ($sale) {
            return response()->json([
                'status' => 'success',
                'message' => "Dispatch Updated by Scanner",
            ], 200);
        }
    }

    public function saleStatusChange(Request $request, $sale_id)
    {
        $sale = Sale::find($sale_id);
        if (!$sale) {
            return response()->json(['message' => 'Sale id not found!']);
        }

        if ($request->status == "Delivered") {
            if ($sale->dispatch_status == 0) {
                return response()->json(['message' => 'Dispatch First!']);
            } else {
                $sale->status = $request->status;
            }
        }

        $account = Account::first();

        if ($request->status == "Delete") {
            $account->available_balance -= $sale->paid_amount;
            $sale->delete();
            return response()->json(['message' => 'Sale Deleted successfully']);
        } else {
            if ($request->status == "Cancel") {
                $account->available_balance -= $sale->paid_amount;
            } else {
                $account->available_balance += $sale->paid_amount;
            }
            $sale->status = $request->status;
            $sale->save();
            return response()->json(['message' => 'Status updated successfully']);
        }

        $account->save();
    }

    public function store_old(SaleRequest $request)
    {
        $sale = new Sale();

        $existingCustomer = Customer::where('phone_number', $request->phone_number)->first();
        if (!$existingCustomer) {

            $request->validate([
                'phone_number' => [
                    'regex:/^(\+?88)?01[3-9]\d{8}$/',
                    'unique:customers,phone_number',
                ],
                'name'     => 'required|string|max:25',
                'email'    => 'nullable|email|unique:customers,email',
                'location' => 'required',
            ], [
                'phone_number.regex' => 'Invalid Bangladeshi Phone Number!',
            ]);

            $newCustomer = new Customer();
            $newCustomer->name = $request->name;
            $newCustomer->email = $request->email;
            $newCustomer->phone_number = $request->phone_number;
            $newCustomer->location = $request->location;
            $newCustomer->save();

            $sale->customer_id = $newCustomer->id;
        } else {
            $sale->customer_id = $existingCustomer->id;

            $existingCustomerUpdate = Customer::where('id', $existingCustomer->id)->first();

            $request->validate([
                'name'     => 'required|string|max:25',
                'email'    => 'nullable|email|unique:employees,email,' . $existingCustomerUpdate->id,
                'location' => 'required',
            ]);

            $existingCustomerUpdate->name = $request->name;
            $existingCustomerUpdate->email = $request->email;
            $existingCustomerUpdate->location = $request->location;
            $existingCustomerUpdate->save();
        }

        $sale->branch_id                   = $request->branch_id;
        $sale->variation_id                = json_encode($request->variation_id);
        $sale->quantity                    = json_encode($request->quantity);
        $sale->unit_price                  = json_encode($request->unit_price);
        $sale->discount_percentage         = json_encode($request->discount_percentage);
        $sale->discount_type_subtotal      = $request->discount_type_subtotal;
        $sale->discount_on_subtotal        = $request->discount_on_subtotal;
        $sale->discount_on_subtotal_amount = $request->discount_on_subtotal_amount;
        $sale->paid_amount                 = $request->paid_amount;
        $sale->shipping_charge             = $request->shipping_charge;
        $sale->courier_id                  = $request->courier_id;
        $sale->delivery_method             = $request->delivery_method;
        $sale->sale_from                   = $request->sale_from;
        $sale->note                        = $request->note;
        $sale->created_by                  = Auth::user()->id;

        $discountAmounts = [];
        $sub_totals = 0;

        /*____________________Discount on Per Product & SubTotal____________________*/

        // foreach ($request->quantity as $key => $quantity) {
        //     $withDiscountPrice = $request->unit_price[$key] - $request->unit_price[$key] * ($request->discount_percentage[$key] / 100);
        //     $discountAmount = $quantity * ($request->unit_price[$key] * ($request->discount_percentage[$key] / 100));
        //     $discountAmounts[] = $discountAmount;

        //     $sub_totals += $quantity * $withDiscountPrice;
        // }
        // $sale->discount_amount = json_encode($discountAmounts);

        $sale->discount_amount = json_encode($request->discount_amount);

        $sale->sub_totals = $request->sub_totals;

        /*____________________Total Amount & Paid Amount & Payment Status____________________*/

        $totalAmount = $sale->total_amount = $request->total_amount;

        $cash       = $sale->cash   = $request->cash;
        $sale->cash_number          = $request->cash_number;
        $bank       = $sale->bank   = $request->bank;
        $sale->bank_number          = $request->bank_number;
        $bkash      = $sale->bkash  = $request->bkash;
        $sale->bkash_number         = $request->bkash_number;
        $nagad      = $sale->nagad  = $request->nagad;
        $sale->nagad_number         = $request->nagad_number;
        $rocket     = $sale->rocket = $request->rocket;
        $sale->rocket_number        = $request->rocket_number;
        $cheque     = $sale->cheque = $request->cheque;
        $sale->cheque_number        = $request->cheque_number;

        $paidAmount = $cash + $bkash + $nagad + $rocket + $bank + $cheque;

        if ($totalAmount < $paidAmount) {
            return response()->json([
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'message' => 'Your Paid Amount High!',
            ], 404);
        }

        $sale->paid_amount = $paidAmount;
        $dueAmount = $sale->due_amount = $totalAmount - $paidAmount;

        if ($paidAmount == $totalAmount) {
            $sale->payment_status = 'paid';
        } elseif ($dueAmount == $totalAmount) {
            $sale->payment_status = 'unpaid';
        } else {
            $sale->payment_status = 'partial';
        }

        /*____________________Variation Stock_Amount Qty____________________*/

        $variationIds = is_array($request->variation_id) ? $request->variation_id : [$request->variation_id];
        $stockIssues = [];
        foreach ($variationIds as $key => $variation) {

            $stockAmountVariation = Variation::find($variation);

            $getStockQuantity = $stockAmountVariation->stock_amount;
            $getSaleQuantity  = $request->quantity[$key];

            if ($getStockQuantity < $getSaleQuantity) {
                $productName   = $stockAmountVariation ? $stockAmountVariation->name : 'Unknown';
                $stockIssues[] = $productName . ' Not Available!';
            } else {
                $currentQuantity = $stockAmountVariation->stock_amount;
                $newQuantity = $currentQuantity - $getSaleQuantity;
                $stockAmountVariation->stock_amount = max(0, $newQuantity);
                $stockAmountVariation->save();
            }
        }

        if (!empty($stockIssues)) {
            return response()->json(['message' => $stockIssues], 404);
        }

        //Sale Invoice & Barcode
        $totalSales = Sale::count();
        $invoiceNumber = str_pad($totalSales + 1, 6, '0', STR_PAD_LEFT);
        $invoice = $sale->invoice = 'INV-SALE-' . $invoiceNumber;

        $barcodePath = public_path("barcodes/{$invoice}.png");

        $generator = new BarcodeGeneratorPNG();
        file_put_contents($barcodePath, $generator->getBarcode($invoice, $generator::TYPE_CODE_128));

        $sale->barcode_path = "barcodes/{$invoice}.png";

        /*____________________ Account balance ____________________*/

        $balance = Account::where('id', '1')->first();
        if ($balance) {
            if (empty($balance->available_balance)) {
                $balance->available_balance = $paidAmount;
            } else {
                $balance->available_balance += $paidAmount;
            }
            $balance->save();
        } else {
            return response()->json(['message' => 'Account Error!'], 404);
        }

        /*____________________ Pathao Courier ____________________*/

        if ($request->courier_id == 1) { //courier_id == 1 (Pathao)
            $customer = Customer::find($sale->customer_id);
            $itemQuantities = json_decode($sale->quantity, true);
            $itemQuantitySum = array_sum($itemQuantities);

            // Prepare shipment data
            if ($request->delivery_method == "cod") {
                $shipmentData = [
                    'merchant_order_id' => $sale->invoice,
                    'recipient_name' => $customer->name,
                    'recipient_phone' => $customer->phone_number,
                    'recipient_address' => $customer->location,
                    'recipient_city' => $request->city_id,
                    'recipient_zone' => $request->zone_id,
                    'item_quantity' => $itemQuantitySum,
                    'item_weight' => 0.5,
                    'recipient_area' => $request->area_id,
                    'amount_to_collect' => $sale->total_amount,
                ];
            } else {
                $shipmentData = [
                    'merchant_order_id' => $sale->invoice,
                    'recipient_name' => $customer->name,
                    'recipient_phone' => $customer->phone_number,
                    'recipient_address' => $customer->location,
                    'recipient_city' => $request->city_id,
                    'recipient_zone' => $request->zone_id,
                    'item_quantity' => $itemQuantitySum,
                    'item_weight' => 0.5,
                    'recipient_area' => $request->area_id,
                    'amount_to_collect' => $sale->due_amount,
                ];
            }

            $pathao = new PathaoCourierApiController;
            $resultData = $pathao->init(['data' => [$shipmentData]]);

            if ($resultData) {
                $sale->status = "Processing";
                $sale->save();

                return response()->json([
                    'status' => 'success',
                    'message' => "Pathao Packed & Shipment",
                    'resultData' => $resultData,
                    'data' => new SaleResource($sale),
                ]);
            } else {
                $sale->dispatch_status = 0;
                $sale->status = "Processing";
                $sale->save();

                return response()->json([
                    'status' => 'error',
                    'message' => "Failed Pathao Courier",
                ], 500);
            }
        }

        $sale->status = 'Processing';
        $sale->save();

        return response()->json([
            'message' => 'Sale created successfully',
            'data' => new SaleResource($sale),
        ], 200);
    }

    public function store(SaleRequest $request)
    {
        DB::beginTransaction();

        try {
            $sale = new Sale();

            $existingCustomer = Customer::where('phone_number', $request->phone_number)->first();
            if (!$existingCustomer) {

                $request->validate([
                    'phone_number' => [
                        'regex:/^(\+?88)?01[3-9]\d{8}$/',
                        'unique:customers,phone_number',
                    ],
                    'name'     => 'required|string|max:25',
                    'email'    => 'nullable|email|unique:customers,email',
                    'location' => 'required',
                ], [
                    'phone_number.regex' => 'Invalid Bangladeshi Phone Number!',
                ]);

                $newCustomer = new Customer();
                $newCustomer->name = $request->name;
                $newCustomer->email = $request->email;
                $newCustomer->phone_number = $request->phone_number;
                $newCustomer->location = $request->location;
                $newCustomer->save();

                $sale->customer_id = $newCustomer->id;
            } else {
                $sale->customer_id = $existingCustomer->id;

                $existingCustomerUpdate = Customer::where('id', $existingCustomer->id)->first();

                $request->validate([
                    'name'     => 'required|string|max:25',
                    'email'    => 'nullable|email|unique:employees,email,' . $existingCustomerUpdate->id,
                    'location' => 'required',
                ]);

                $existingCustomerUpdate->name = $request->name;
                $existingCustomerUpdate->email = $request->email;
                $existingCustomerUpdate->location = $request->location;
                $existingCustomerUpdate->save();
            }

            $sale->branch_id                   = $request->branch_id;
            $sale->variation_id                = json_encode($request->variation_id);
            $sale->quantity                    = json_encode($request->quantity);
            $sale->unit_price                  = json_encode($request->unit_price);
            $sale->discount_percentage         = json_encode($request->discount_percentage);
            $sale->discount_type_subtotal      = $request->discount_type_subtotal;
            $sale->discount_on_subtotal        = $request->discount_on_subtotal;
            $sale->discount_on_subtotal_amount = $request->discount_on_subtotal_amount;
            $sale->paid_amount                 = $request->paid_amount;
            $sale->shipping_charge             = $request->shipping_charge;
            $sale->courier_id                  = $request->courier_id;
            $sale->delivery_method             = $request->delivery_method;
            $sale->sale_from                   = $request->sale_from;
            $sale->note                        = $request->note;
            $sale->created_by                  = "1";

            $sale->sub_totals = $request->sub_totals;

            /*____________________Total Amount & Paid Amount & Payment Status____________________*/

            $totalAmount = $sale->total_amount = $request->total_amount;

            $cash       = $sale->cash   = $request->cash;
            $sale->cash_number          = $request->cash_number;
            $bank       = $sale->bank   = $request->bank;
            $sale->bank_number          = $request->bank_number;
            $bkash      = $sale->bkash  = $request->bkash;
            $sale->bkash_number         = $request->bkash_number;
            $nagad      = $sale->nagad  = $request->nagad;
            $sale->nagad_number         = $request->nagad_number;
            $rocket     = $sale->rocket = $request->rocket;
            $sale->rocket_number        = $request->rocket_number;
            $cheque     = $sale->cheque = $request->cheque;
            $sale->cheque_number        = $request->cheque_number;

            $paidAmount = $cash + $bkash + $nagad + $rocket + $bank + $cheque;

            if ($totalAmount < $paidAmount) {
                return response()->json([
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'message' => 'Your Paid Amount High!',
                ], 404);
            }

            $sale->paid_amount = $paidAmount;
            $dueAmount = $sale->due_amount = $totalAmount - $paidAmount;

            if ($paidAmount == $totalAmount) {
                $sale->payment_status = 'paid';
            } elseif ($dueAmount == $totalAmount) {
                $sale->payment_status = 'unpaid';
            } else {
                $sale->payment_status = 'partial';
            }

            /*____________________Variation Stock_Amount Qty____________________*/

            $variationIds = is_array($request->variation_id) ? $request->variation_id : [$request->variation_id];
            $stockIssues = [];
            foreach ($variationIds as $key => $variation) {

                $stockAmountVariation = Variation::find($variation);

                $getStockQuantity = $stockAmountVariation->stock_amount;
                $getSaleQuantity  = $request->quantity[$key];

                if ($getStockQuantity < $getSaleQuantity) {
                    $productName   = $stockAmountVariation ? $stockAmountVariation->name : 'Unknown';
                    $stockIssues[] = $productName . ' Not Available!';
                } else {
                    $currentQuantity = $stockAmountVariation->stock_amount;
                    $newQuantity = $currentQuantity - $getSaleQuantity;
                    $stockAmountVariation->stock_amount = max(0, $newQuantity);
                    $stockAmountVariation->save();
                }
            }

            if (!empty($stockIssues)) {
                return response()->json(['message' => $stockIssues], 404);
            }

            //Sale Invoice & Barcode
            $totalSales = Sale::count();
            $invoiceNumber = str_pad($totalSales + 1, 6, '0', STR_PAD_LEFT);
            $invoice = $sale->invoice = 'INV-SALE-' . $invoiceNumber;

            $barcodePath = public_path("barcodes/{$invoice}.png");

            $generator = new BarcodeGeneratorPNG();
            file_put_contents($barcodePath, $generator->getBarcode($invoice, $generator::TYPE_CODE_128));

            $sale->barcode_path = "barcodes/{$invoice}.png";

            /*____________________ Account balance ____________________*/

            $balance = Account::where('id', '1')->first();
            if ($balance) {
                if (empty($balance->available_balance)) {
                    $balance->available_balance = $paidAmount;
                } else {
                    $balance->available_balance += $paidAmount;
                }
                $balance->save();
            } else {
                return response()->json(['message' => 'Account Error!'], 404);
            }

            /*____________________ Pathao Courier ____________________*/

            if ($request->courier_id == 1) { //courier_id == 1 (Pathao)
                $customer = Customer::find($sale->customer_id);
                $itemQuantities = json_decode($sale->quantity, true);
                $itemQuantitySum = array_sum($itemQuantities);

                // Prepare shipment data
                if ($request->delivery_method == "cod") {
                    $shipmentData = [
                        'merchant_order_id' => $sale->invoice,
                        'recipient_name' => $customer->name,
                        'recipient_phone' => $customer->phone_number,
                        'recipient_address' => $customer->location,
                        'recipient_city' => $request->city_id,
                        'recipient_zone' => $request->zone_id,
                        'item_quantity' => $itemQuantitySum,
                        'item_weight' => 0.5,
                        'recipient_area' => $request->area_id,
                        'amount_to_collect' => $sale->total_amount,
                    ];
                } else {
                    $shipmentData = [
                        'merchant_order_id' => $sale->invoice,
                        'recipient_name' => $customer->name,
                        'recipient_phone' => $customer->phone_number,
                        'recipient_address' => $customer->location,
                        'recipient_city' => $request->city_id,
                        'recipient_zone' => $request->zone_id,
                        'item_quantity' => $itemQuantitySum,
                        'item_weight' => 0.5,
                        'recipient_area' => $request->area_id,
                        'amount_to_collect' => $sale->due_amount,
                    ];
                }

                $pathao = new PathaoCourierApiController;
                $resultData = $pathao->init(['data' => [$shipmentData]]);

                if ($resultData) {
                    $sale->status = "Processing";
                    $sale->save();

                    return response()->json([
                        'status' => 'success',
                        'message' => "Pathao Packed & Shipment",
                        'resultData' => $resultData,
                        'data' => new SaleResource($sale),
                    ]);
                } else {
                    $sale->dispatch_status = 0;
                    $sale->status = "Processing";
                    $sale->save();

                    return response()->json([
                        'status' => 'error',
                        'message' => "Failed Pathao Courier",
                    ], 500);
                }
            }

            $sale->status = 'Processing';
            $sale->save();

            DB::commit();

            return response()->json([
                'message' => 'Sale created successfully',
                'data' => new SaleResource($sale),
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Error creating sale: ' . $e->getMessage() . $e->getLine(),
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
        $sale = Sale::find($id);
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }
        return new SaleResource($sale);
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
        DB::beginTransaction();

        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return response()->json([
                    'message' => "No Sale Found for Id - " . $id,
                ]);
            }

            $existingCustomer = Customer::where('id', $request->customer_id)->first();
            if ($existingCustomer) {
                $request->validate([
                    'name'     => 'required|string|max:25',
                ]);
                $existingCustomer->name = $request->name;
                $existingCustomer->email = $request->email;
                $existingCustomer->location = $request->location;
                $existingCustomer->update();
            }

            //Update : Variation / Unit Price / Quantity / Discount % / Discount Amount

            $vId = $request->input('variation_id');
            $sale->variation_id = json_encode($vId);

            $uP = $request->input('unit_price');
            $sale->unit_price = json_encode($uP);

            $qty = $request->input('quantity');
            $sale->quantity = json_encode($qty);

            $dA = $request->input('discount_amount');
            $dA = array_map('intval', $dA);
            $sale->discount_amount = json_encode($dA);

            $sale->sub_totals = $request->sub_totals;
            $sale->discount_on_subtotal_amount = $request->discount_on_subtotal_amount;
            $sale->shipping_charge = $request->shipping_charge;
            $sale->total_amount = $request->total_amount;
            $sale->due_amount = $request->total_amount;

            $sale->sale_from = $request->sale_from;
            $sale->note = $request->note;
            $sale->courier_id = $request->courier_id;


            /*____________________Variation Stock_Amount Qty____________________*/

            $variationIds = is_array($request->variation_id) ? $request->variation_id : [$request->variation_id];
            $stockIssues = [];
            foreach ($variationIds as $key => $variation) {

                $stockAmountVariation = Variation::find($variation);

                $getStockQuantity = $stockAmountVariation->stock_amount;
                $getSaleQuantity  = $request->quantity[$key];

                if ($getStockQuantity < $getSaleQuantity) {
                    $productName   = $stockAmountVariation ? $stockAmountVariation->name : 'Unknown';
                    $stockIssues[] = $productName . ' Not Available!';
                } else {
                    $currentQuantity = $stockAmountVariation->stock_amount;
                    $newQuantity = $currentQuantity - $getSaleQuantity;
                    $stockAmountVariation->stock_amount = max(0, $newQuantity);
                    $stockAmountVariation->save();
                }
            }

            if (!empty($stockIssues)) {
                return response()->json(['message' => $stockIssues], 404);
            }

            $sale->update();

            DB::commit();

            return response()->json([
                'message' => $sale->invoice . " Updated Successfully!",
                'data' => new SaleSingleResource($sale),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('An error occurred: ' . $e->getMessage());

            return response()->json([
                'message' => 'Sale Update Failed!',
                'error' => $e->getMessage(),
            ], 500);
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
        // $sale = Sale::find($id);
        // if (!$sale) {
        //     return response()->json(['message' => 'Sale not found'], 404);
        // }
        // $sale->delete();

        // return response()->json(['message' => 'Sale data deleted successfully'], 200);
    }

    public function saleDuePayment(Request $request, $sale_id)
    {
        $sale = Sale::find($sale_id);
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        $dueCash       = $request->cash;
        $sale->cash   += $dueCash;
        $dueBank       = $request->bank;
        $sale->bank   += $dueBank;
        $dueBkash      = $request->bkash;
        $sale->bkash  += $dueBkash;
        $dueNagad      = $request->nagad;
        $sale->nagad  += $dueNagad;
        $dueRocket     = $request->rocket;
        $sale->rocket += $dueRocket;
        $dueCheque     = $request->cheque;
        $sale->cheque += $dueCheque;

        $duePaidAmount = $dueCash + $dueBank + $dueBkash + $dueNagad + $dueRocket + $dueCheque;

        if ($sale->due_amount < $duePaidAmount) {
            return response()->json([
                'sale_dueAmount' => $sale->due_amount,
                'paid_amount' => $duePaidAmount,
                'message' => 'Your Paid Amount High!',
            ], 404);
        }

        if ($duePaidAmount) {
            $salePaidAmount = $sale->paid_amount;
            $saleDueAmount  = $sale->due_amount;

            $updateDueAmount = $sale->due_amount = $saleDueAmount - $duePaidAmount;
            $updatePaidAmount = $sale->paid_amount = $salePaidAmount + $duePaidAmount;

            $sale->paid_amount = $updatePaidAmount;
            $sale->due_amount = $updateDueAmount;

            if ($sale->due_amount == 0) {
                $sale->payment_status = 'paid';
            } else {
                $sale->payment_status = 'partial';
            }

            $sale->note = $request->note;

            /*____________________Account balance____________________*/

            $balance = Account::where('id', '1')->first();
            if ($balance) {
                if (empty($balance->available_balance)) {
                    $balance->available_balance = $duePaidAmount;
                } else {
                    $balance->available_balance += $duePaidAmount;
                }
                $balance->save();
            } else {
                return response()->json(['message' => 'Account Error!'], 404);
            }

            $sale->save();
        }

        return response()->json([
            'message' => 'Sale Due Payment successfully',
            'data' => new SaleResource($sale),
        ], 200);
    }

    ////////////////////////// Sale Reschedule ////////////////////////////

    public function saleReschedule()
    {
        $saleReschedule = SaleReschedule::latest()->get();
        return response()->json([
            'sale_reschedule' => SaleRescheduleResource::collection($saleReschedule),
        ], 200);
    }

    public function saleRescheduleStore(Request $request, $sale_id)
    {
        $request->validate([
            'reschedule_date' => 'required'
        ]);

        $sale = Sale::find($sale_id);
        if (!$sale) {
            return response()->json([
                'message' => "Sale id not found!"
            ], 404);
        }

        if ($sale->status == 'Reschedule') {
            return response()->json([
                'message' => "Already Reschedule!"
            ], 404);
        }

        $sale_reschedule =  new SaleReschedule();
        $sale_reschedule->sale_id = $sale->id;
        $sale_reschedule->reschedule_date = $request->reschedule_date;
        $sale_reschedule->note = $request->note;
        $sale_reschedule->save();

        if ($sale_reschedule) {
            $sale->status = "Reschedule";
            $sale->save();
        }

        return response()->json([
            'message' => "Sale-Reschedule Successful!",
            'sale_reschedule' => new SaleRescheduleResource($sale_reschedule),
        ], 200);
    }

    public function saleRescheduleShow($id)
    {
        $sale_reschedule = SaleReschedule::find($id);
        if (!$sale_reschedule) {
            return response()->json([
                'message' => "Invalid SaleReschedule id!"
            ], 404);
        }

        return response()->json([
            'message' => "Sale-Reschedule Update Successful!",
            'sale_reschedule' => new SaleRescheduleResource($sale_reschedule),
        ], 200);
    }

    public function saleRescheduleUpdate(Request $request, $id)
    {
        $request->validate([
            'reschedule_date' => 'required',
        ]);

        $sale_reschedule = SaleReschedule::find($id);
        if (!$sale_reschedule) {
            return response()->json([
                'message' => "Invalid SaleReschedule id!"
            ], 404);
        }

        $sale_reschedule->reschedule_date = $request->reschedule_date;
        $sale_reschedule->note = $request->note;
        $sale_reschedule->save();

        return response()->json([
            'message' => "Sale-Reschedule Update Successful!",
            'sale_reschedule' => new SaleRescheduleResource($sale_reschedule),
        ], 200);
    }

    public function saleRescheduleProcess(Request $request, $sale_id)
    {
        $sale = Sale::find($sale_id);
        if (!$sale) {
            return response()->json([
                'message' => "Invalid Sale id!"
            ], 404);
        }

        $sale_reschedule = SaleReschedule::where('sale_id', $sale->id)->first();
        if (!$sale_reschedule) {
            return response()->json([
                'message' => "Sale id not exist on SaleReschedule!"
            ], 404);
        } else {
            $sale_reschedule->delete();
        }

        $sale->status = "Processing";
        $sale->save();


        return response()->json([
            'message' => "Invoice On Sale Processing!",
            // 'sale_reschedule' => new SaleRescheduleResource($sale_reschedule),
        ], 200);
    }

    public function saleReportDateFilter(Request $request)
    {
        $startDate = null;
        $endDate = null;

        if ($request->filter) {
            switch ($request->filter) {
                case 'today':
                    $startDate = Carbon::today()->toDateString();
                    $endDate = Carbon::today()->toDateString();
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday()->toDateString();
                    $endDate = Carbon::yesterday()->toDateString();
                    break;
                case 'last_seven_days':
                    $startDate = Carbon::today()->subDays(6)->toDateString();
                    $endDate = Carbon::today()->toDateString();
                    break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                    $endDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                    break;
                case 'this_year':
                    $startDate = Carbon::now()->startOfYear()->toDateString();
                    $endDate = Carbon::now()->endOfYear()->toDateString();
                    break;
                case 'date_range':
                    $startDate = Carbon::parse($request->start_date)->toDateString();
                    $endDate = Carbon::parse($request->end_date)->toDateString();
                    break;
                default:
                    return response()->json(['error' => 'Invalid filter'], 400);
            }

            $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->get();
        } else {
            $date = $request->date;
            $filterDate = Carbon::parse($date)->toDateString();
            $sales = Sale::whereDate('created_at', $filterDate)->get();
        }

        return response()->json(['sales' => $sales], 200);
    }
}
