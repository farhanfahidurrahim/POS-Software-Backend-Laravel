<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierTransactionPaymentResource extends JsonResource
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
            'supplier_transaction_id' => $this->supplier_transaction_id,
            'supplier_id' => [
                'id' => $this->getSupplier->id,
                'name' => $this->getSupplier->name,
                'phone_number' => $this->getSupplier->phone_number,
            ],
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,
            'note' => $this->note,
            'document' =>config('app.url')."/". config('imagepath.suppliertransaction')."/".$this->document,
        ];
    }
}