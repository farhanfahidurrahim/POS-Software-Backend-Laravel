<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepositResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $total_expense = $this->additional['totalExpense'] ?? null;

        return [
            'id' => $this->id,
            'present_balance' => $this->present_balance,
            'previous_balance' => $this->previous_balance,
            'amount' => $this->amount,
            'note' => $this->note,
            'created_by' => $this->getUser->name,
            'created_at' => $this->created_at->format('d M y, g:i:s A'),
        ];
    }
}
