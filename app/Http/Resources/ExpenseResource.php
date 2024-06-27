<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            "expense_category" => new ExpenseCategoryResource($this->getExpenseCategory),
            "date" => $this->date,
            "expense_reason" => $this->expense_reason,
            "total_amount" => $this->total_amount,
            'document' =>config('app.url')."/". config('imagepath.expense')."/".$this->document,
            'expensePayment' => new ExpensePaymentResource($this->expensePayment),
        ];
    }
}
