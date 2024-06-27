<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpensePaymentResource extends JsonResource
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
            // "expense" => $this->expense_id,
            "amount" => $this->amount,
            "paid_on" => $this->paid_on,
            "payment_method" => $this->payment_method,
            "payment_account" => $this->payment_account,
            "payment_id" => $this->payment_id,
            "expense_payment_status" => $this->expense_payment_status,
            "note" => $this->note,
        ];
    }
}
