<?php

namespace App\Http\Resources;

use App\Models\Variation;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray($request)
    {
        $variationIds = json_decode($this->variation_id);
        $variations = Variation::whereIn('id', $variationIds)->get();
        return [
            "id" => $this->id,
            "purchase_date" => $this->purchase_date,
            "invoice" => $this->invoice,
            "supplier_id" => new SupplierResource($this->getSupplier),
            "product_id" => ProductResource::collection($this->product),
            "variation_id" => VariationResource::collection($variations),
            "quantity" => json_decode($this->quantity),
            "unit_price" => json_decode($this->unit_price),
            "sub_total" => json_decode($this->sub_total),
            "sub_total_amount" => $this->sub_total_amount,
            "shipping_amount" => $this->shipping_amount,
            "total_amount" => $this->total_amount,
            "paid_amount" => $this->paid_amount,
            "due_amount" => $this->due_amount,
            "purchase_status" => $this->purchase_status,
            "payment_status" => $this->payment_status,
            "payment_method" => $this->payment_method,
            "note" => $this->note,
            "document" => config('app.url') . "/" . config('imagepath.purchase') . "/" . $this->document,
            "return_purchase" => json_decode($this->return_purchase),
            "created_by" => $this->getUser->name,
        ];
    }
}
