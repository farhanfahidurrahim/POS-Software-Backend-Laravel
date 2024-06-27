<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\SupplierTransactionPaymentResource;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\SupplierTransaction;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\SupplierResource;
use App\Models\SupplierTransactionPayment;
use App\Http\Requests\Supplier\SupplierRequest;
use App\Http\Resources\SupplierPurchaseResource;
use App\Http\Resources\SupplierTransactionResource;
use App\Http\Controllers\API\BaseController as BaseController;

class SupplierController extends BaseController
{

    public function index()
    {
        $supplier = Supplier::latest()->get();

        return SupplierResource::collection($supplier);
    }

    // return $this->sendResponse(SupplierResource::collection($supplier), 'Suppler retrieved successfully.');

    public function create()
    {
        //
    }

    public function store(SupplierRequest $request)
    {
        try {
            $validatedData = $request->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Validation Error.', $e->errors());
        }
        $supplier = new Supplier();
        $supplier->fill($validatedData);
        $supplier->save();
        return $this->sendResponse($supplier, 'Supplier Created Successfully.');
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $supplierPurchase = Purchase::where('supplier_id', $supplier->id)->get();

        return new SupplierPurchaseResource(['supplier' => $supplier, 'supplierPurchase' => $supplierPurchase]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'phone_number' => [
                'required',
                'regex:/^(\+?88)?01[3-9]\d{8}$/',
                'unique:suppliers,phone_number,' . $id,
            ],
            'email' => 'nullable|email|unique:suppliers,email,' . $id,
            'company_name' => 'nullable|string',
            'company_number' => [
                'nullable',
                'regex:/^(\+?88)?01[3-9]\d{8}$/',
                'unique:suppliers,company_number,' . $id,
            ],
            'location' => 'nullable|string',
        ], [
            'phone_number.regex' => 'Invalid Bangladeshi phone number.',
            'company_number.regex' => 'Invalid Bangladeshi phone number.',
        ]);

        $supplier = Supplier::find($id);

        if (!$supplier) {
            return $this->sendError('Supplier not found.');
        }

        $supplier->update([
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'company_name' => $validatedData['company_name'],
            'company_number' => $validatedData['company_number'],
            'location' => $validatedData['location'],
        ]);

        return $this->sendResponse($supplier, 'Supplier Updated Successfully.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
