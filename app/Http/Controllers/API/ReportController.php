<?php

namespace App\Http\Controllers\API;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportPurchaseResource;
use App\Models\Sale;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function report(Request $request, $model)
    {
        if ($model == "sale") {
            $query = Sale::query();

            // Check if start_date and end_date are provided and not null
            if ($request->has('start_date') && $request->has('end_date')) {
                if ($request->start_date != 'null') {
                    try {
                        // Parse start_date and end_date using Carbon
                        $start_date = Carbon::createFromFormat('m/d/Y', $request->input('start_date'))->startOfDay();
                        $end_date = Carbon::createFromFormat('m/d/Y', $request->input('end_date'))->endOfDay();

                        // Apply date filters to the query
                        $query->whereBetween('created_at', [$start_date, $end_date]);
                    } catch (\Exception $e) {
                        return response()->json(['error' => 'Invalid date format. Use format m/d/Y for start_date and end_date.'], 400);
                    }
                }
            }

            // Fetch all sales based on the query
            $sales = $query->get();

            // Calculate totals
            $totalSale = $sales->where('status', '!=', 'Cancel')->count();
            $totalQuantitySales = $sales->where('status', '!=', 'Cancel')->reduce(function ($carry, $sale) {
                $quantityArray = json_decode($sale->quantity, true);
                if (is_array($quantityArray)) {
                    return $carry + array_sum($quantityArray);
                }
                return $carry;
            }, 0);
            $totalAmount = $sales->sum('total_amount');
            $paidAmount = $sales->sum('paid_amount');
            $dueAmount = $sales->sum('due_amount');
            $shipping_charge = $sales->sum('shipping_charge');
            $totalCancelSale = $sales->where('status', 'Cancel')->count();
            $totalCancelSaleAmount = $sales->where('status', 'Cancel')->sum('total_amount');

            return response()->json([
                'total_sale' => $totalSale,
                'total_saleQuantity' => $totalQuantitySales,
                'total_saleAmount' => number_format($totalAmount, 2, '.', ''),
                'total_paidAmount' => number_format($paidAmount, 2, '.', ''),
                'total_dueAmount' => number_format($dueAmount, 2, '.', ''),
                'total_shippingCharge' => number_format($shipping_charge, 2, '.', ''),
                'total_cancelSale' => $totalCancelSale,
                'total_cancelSaleAmount' => number_format($totalCancelSaleAmount, 2, '.', ''),
            ]);
        }

        return response()->json(['message' => 'Invalid model'], 400);
    }
}
