<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportPurchaseResource extends JsonResource
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
            'invoice' => $this->invoice,
            'date' => $this->created_at->format('d M y, g:i:s A'),
            'supplier_id' => $this->supplier_id,
            'purchased_by' => $this->created_by,
            'Purchase Qty' => json_decode($this->quantity),
            'Total' => $this->total_amount,
            'Paid' => $this->paid_amount,
            'Due' => $this->due_amount,
        ];
    }
}
