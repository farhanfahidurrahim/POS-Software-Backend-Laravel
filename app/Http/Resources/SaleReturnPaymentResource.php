<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleReturnPaymentResource extends JsonResource
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
            'sale_return' => new SaleReturnResource($this->getSaleReturn),
            'customer' => new CustomerResource($this->getCustomer),
            'bkash' => $this->bkash,
            'nagad' => $this->nagad,
            'rocket' => $this->rocket,
            'bank' => $this->bank,
            'cheque' => $this->cheque,
            'cash' => $this->cash,
            'return_paid_amount' => $this->return_paid_amount,
            'payment_status' => $this->payment_status,
            'document' =>config('app.url')."/". config('imagepath.salereturnpayment')."/".$this->document,
            'note' => $this->note,
            'created_by' => $this->getUser->name,
            'created_at' => $this->created_at->format('d M y, g:i:s A'),
        ];
    }
}
