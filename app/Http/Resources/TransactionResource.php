<?php

namespace App\Http\Resources;

use App\Models\Variation;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "date"=> $this->date,
            "branch" => new BranchResource($this->getBranch),
            "customer" => new CustomerResource($this->getCustomer),
            "supplier" => new SupplierResource($this->getSupplier),
            // "product" => new ProductResource($this->getProduct),
            "variation"=> VariationResource::collection($variations),
            "expense" => new ExpenseResource($this->getExpense),
            "purchase" => new PurchaseResource($this->getPurchase),
            "sale" => new SaleResource($this->getSale),
            "transaction_type" => $this->transaction_type,
            "transaction_amount" => $this->transaction_amount,
            "cash" => $this->cash,
            "bank" => $this->bank,
            "bkash" => $this->bkash,
            "nagad" => $this->nagad,
            "rocket" => $this->rocket,
            "payment_status" => $this->payment_status,
        ];
    }
}
