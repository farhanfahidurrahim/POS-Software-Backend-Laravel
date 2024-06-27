<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchasePaymentResource extends JsonResource
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
            'id' => $this->id,
            'date' => $this->date,
            'purchase' => new PurchaseResource($this->getPurchase),
            'reference' => $this->reference,
            'paid_amount' => $this->paid_amount,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'note' => $this->note,
            'created_by' => $this->getAuthUser->name,
            // 'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // 'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
