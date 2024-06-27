<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchasePaymentReturnResource extends JsonResource
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
            "id"=> $this->id,
            'purchase_return' => new PurchaseReturnResource($this->getPurchaseReturn),
            'user' => $this->getAuthUser ? $this->getAuthUser->name : null,
            'amount' => $this->amount,
            'date' => $this->date,
            'reference' => $this->reference,
            'payment_method' => $this->payment_method,
            'note' => $this->note,
        ];
    }
}
