<?php

namespace App\Http\Resources;

use App\Models\Variation;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleReturnResource extends JsonResource
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
            "id" => $this->id,
            'sale' => new SaleResource($this->getSale),
            'sale_quantity' => json_decode($this->getSale->quantity),
            'customer' => new CustomerResource($this->getCustomer),
            'branch' => new BranchResource($this->getBranch),
            "variation_id" => VariationResource::collection($variations),
            //'unit_price' => $this->unit_price,
            'unit_price' => json_decode($this->unit_price),
            'return_quantity' => json_decode($this->return_quantity),
            //'return_quantity' => $this->return_quantity,
            'discount_amount' => json_decode($this->discount_amount),
            'sub_totals' => $this->sub_totals,
            'discount_on_subtotal_amount' => $this->discount_on_subtotal_amount,
            'return_amount' => $this->return_amount,
            'payment_status' => $this->payment_status,
            'document' => config('app.url') . "/" . config('imagepath.salereturn') . "/" . $this->document,
            'note' => $this->note,
            'created_by' => $this->getAuthUser->name,
            'created_at' => $this->created_at->format('d M y, g:i:s A'),
        ];
    }
}
