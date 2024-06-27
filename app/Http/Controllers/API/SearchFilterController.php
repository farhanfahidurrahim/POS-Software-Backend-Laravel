<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\SaleResource;
use App\Http\Resources\CustomerResource;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use Carbon\Carbon;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SaleIndexResource;

class SearchFilterController extends Controller
{
    public function search(Request $request, $keyword)
    {
        if (!empty($keyword)) {
            if ($request->model == 'category') {
                $categories = Category::where("name", "like", "%{$keyword}%")->get();
                if ($categories->isEmpty()) {
                    return response()->json([
                        'message' => "No Searching Data Found!"
                    ], 404);
                }
                return CategoryResource::collection($categories);
            } elseif ($request->model == 'sale') {
                $sale = Sale::with('getCustomer', 'getCourier')
                    ->where("invoice", "like", "%{$keyword}%")
                    ->orWhere("created_at", "like", "%{$keyword}%")
                    ->orWhere("sale_from", "like", "%{$keyword}%")
                    ->orWhere("dispatch_status", "like", "%{$keyword}%")
                    ->orWhere("status", "like", "%{$keyword}%")
                    ->orWhereHas('getCustomer', function ($query) use ($keyword) {
                        $query->where("name", "like", "%{$keyword}%")
                            ->orWhere("phone_number", "like", "%{$keyword}%");
                    })
                    ->orWhereHas('getCourier', function ($query) use ($keyword) {
                        $query->where("name", "like", "%{$keyword}%");
                    })
                    ->latest()->get();

                if ($sale->isEmpty()) {
                    return response()->json([
                        'data' => [],
                    ], 404);
                }
                return SaleIndexResource::collection($sale);
            } elseif ($request->model == 'customer') {
                $customer = collect();
                $customer = Customer::where("name", "like", "%{$keyword}%")
                    ->orWhere("location", "like", "%{$keyword}%")
                    ->orWhere("phone_number", "like", "%{$keyword}%")
                    ->orWhere("email", "like", "%{$keyword}%")
                    ->get();
                if ($customer->isEmpty()) {
                    return response()->json([
                        'message' => "No Searching Data Found!"
                    ], 404);
                }
                return CustomerResource::collection($customer);
            } elseif ($request->model == 'dispatch') {
                // dd($keyword);
                $sales = Sale::with(['getCourier', 'getCustomer'])
                    ->where("invoice", "like", "%{$keyword}%")
                    ->orWhere("created_at", "like", "%{$keyword}%")
                    ->orWhere("sale_from", "like", "%{$keyword}%")
                    ->orWhere("dispatch_status", "like", "%{$keyword}%")
                    ->orWhere("dispatch_date", "like", "%{$keyword}%")
                    ->orWhere("status", "like", "%{$keyword}%")
                    ->orWhereHas('getCustomer', function ($query) use ($keyword) {
                        $query->where("name", "like", "%{$keyword}%")
                            ->orWhere("phone_number", "like", "%{$keyword}%");
                    })
                    ->orWhereHas('getCourier', function ($query) use ($keyword) {
                        $query->where("name", "like", "%{$keyword}%");
                    })
                    ->latest()->get();
                // dd($sales);
                $saleList = [];
                foreach ($sales as $sale) {
                    $dispatchDate = $sale->dispatch_date ? $sale->dispatch_date : '';
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
                    ],
                ]);
            } else {
                return response()->json([
                    'message' => "No Searching Data Found!"
                ], 404);
            }
        }
        // else {
        //     return response()->json([
        //         'message' => "No Searching Data Found!"
        //     ], 404);
        // }
    }

    public function showEntries(Request $request, $number)
    {
        if ($request->model == 'sale') {
            $sale = Sale::latest()->paginate($number);
            return SaleIndexResource::collection($sale);
        } elseif ($request->model == 'dispatch') {
            $sales = Sale::with(['getCourier', 'getCustomer'])->latest()->paginate($number);
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
        } elseif ($request->model == 'customer') {
            $customer = Customer::latest()->paginate($number);
            return CustomerResource::collection($customer);
        }
    }

    public function dateFilters(Request $request, $model)
    {
        if ($request->has('this_week')) {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();

            $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->get();

            if ($sales->isEmpty()) {
                return response()->json([
                    'message' => 'No data found specified date.'
                ], 404);
            }

            return SaleResource::collection($sales);
        } else {
            $request->validate([
                'start_date' => 'required|date_format:m/d/Y',
                'end_date' => 'required|date_format:m/d/Y',
            ]);

            $startDate = Carbon::createFromFormat('m/d/Y', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $request->end_date)->endOfDay();

            if ($model == 'sale') {
                $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->get();
            }

            if ($sales->isEmpty()) {
                return response()->json([
                    'data' => [],
                ], 200);
            }

            return SaleIndexResource::collection($sales);
        }
    }

    public function csvDownload(Request $request, $model)
    {
        $data = [];

        $query = Sale::query();

        if (
            $request->has('start_date') && $request->has('end_date') &&
            !is_null($request->start_date) && $request->start_date !== 'null' &&
            !is_null($request->end_date) && $request->end_date !== 'null'
        ) {
            $request->validate([
                'start_date' => 'required|date_format:m/d/Y',
                'end_date' => 'required|date_format:m/d/Y',
            ]);

            $startDate = Carbon::createFromFormat('m/d/Y', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', $request->end_date)->endOfDay();

            $query->whereBetween('sales.created_at', [$startDate, $endDate]);
        }

        if ($model == 'sale') {
            $results = $query->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->join('courier_shippings', 'sales.courier_id', '=', 'courier_shippings.id')
                ->select(
                    'sales.created_at as date',
                    'sales.invoice',
                    'customers.name as customer_name',
                    'customers.phone_number',
                    'customers.location',
                    'courier_shippings.name as courier_name',
                    'sales.total_amount',
                    'sales.paid_amount',
                    'sales.due_amount'
                )
                ->get();
        } else {
            $data = collect();
        }

        foreach ($results as $index => $row) {
            $data[] = [
                'SL' => $index + 1,
                'Date' => Carbon::parse($row->date)->format('Y-m-d'),
                'invoiceNo' => $row->invoice ?? 'N/A',
                'customerName' => $row->customer_name,
                'phone' => $row->phone_number,
                'address' => $row->location,
                'courierName' => $row->courier_name,
                'totalAmount' => $row->total_amount,
                'paidAmount' => $row->paid_amount,
                'dueAmount' => $row->due_amount
            ];
        }

        return response()->json($data);
    }
}
