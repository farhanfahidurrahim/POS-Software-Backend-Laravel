<?php

namespace App\Http\Resources;

use App\Models\Variation;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleSingleResource extends JsonResource
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
        // dd($variationIds);
        $variations = Variation::whereIn('id', $variationIds)->get();
        // dd($variations);

        return [
            "id" => $this->id,
            "invoice" => $this->invoice,
            //"barcode_path" => $this->barcode_path,
            'barcode_path' => config('app.url') . "/" . $this->barcode_path,
            'customer' => new CustomerResource($this->getCustomer),
            "variation_id" => VariationPosResource::collection($variations),
            "quantity" => json_decode($this->quantity),
            "total_qty" => array_sum(json_decode($this->quantity)),
            "unit_price" => json_decode($this->unit_price),
            "discount_amount" => json_decode($this->discount_amount),
            "sub_totals" => json_decode($this->sub_totals),
            "discount_type_subtotal" => $this->discount_type_subtotal,
            "discount_on_subtotal" => $this->discount_on_subtotal,
            "discount_on_subtotal_amount" => $this->discount_on_subtotal_amount,
            'shipping_charge' => $this->shipping_charge,
            'courier_id' => new CourierShippingResource($this->getCourier),
            'delivery_method' => $this->delivery_method,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,
            'payment_status' => $this->payment_status,
            'sale_from' => $this->sale_from,
            'note' => $this->note,
            'status' => $this->status,
            'sale_return' => $this->sale_return,
            'created_by' => $this->getUser->name,
            'created_at' => $this->created_at->format('d M y, g:i:s A'),
        ];
    }
}
