<?php

namespace App\Http\Controllers\API;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportPurchaseResource;

class ReportController extends Controller
{
    public function reportPurchase()
    {
        $purchase = Purchase::latest()->get();
        if($purchase->isEmpty()){
            return response()->json(['message' => 'No purchase report found'], 200);
        }
        return ReportPurchaseResource::collection($purchase);
    }
}
