<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Variation;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            "id" => $this->id,
            "invoice" => $this->invoice,
            'customer' => [
                'name' => $this->getCustomer->name,
                'phone_number' => $this->getCustomer->phone_number,
                'location' => $this->getCustomer->location,
            ],
            "quantity" => json_decode($this->quantity),
            "total_qty" => array_sum(json_decode($this->quantity)),
            "unit_price" => json_decode($this->unit_price),
            'courier_id' => new CourierShippingResource($this->getCourier),
            'shipping_charge' => $this->shipping_charge,
            'delivery_method' => $this->delivery_method,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,
            'payment_status' => $this->payment_status,
            'sale_from' => $this->sale_from,
            'dispatch_status' => $this->dispatch_status == 1 ? "Yes" :  "No",
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
