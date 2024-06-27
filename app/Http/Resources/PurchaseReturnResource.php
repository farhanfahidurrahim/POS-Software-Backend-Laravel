<?php

namespace App\Http\Resources;

use App\Models\Variation;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseReturnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $variationIds = json_decode($this->variation_id);
        $variations = Variation::whereIn('id', $variationIds)->get();
        return [
            "id"=> $this->id,
            "supplier_id"=> new SupplierResource($this->supplier),
            "user_id"=> $this->user_id,
            "purchase_id"=> new PurchaseResource($this->purchase),
            "variation_id"=> VariationResource::collection($variations),
            "reference"=> $this->reference,
            "return_quantity"=> json_decode($this->return_quantity),
            "unit_price_with_all="=> json_decode($this->unit_price_with_all),
            "total_amount"=> $this->total_amount,
            "paid_amount"=> $this->paid_amount,
            "due_amount"=> $this->due_amount,
            "status"=> $this->status,
            "payment_status"=> $this->payment_status,
            "payment_method"=> $this->payment_method,
            "note"=> $this->note,
        ];
    }
}
