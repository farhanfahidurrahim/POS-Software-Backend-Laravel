<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
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
            "income_category_id" => [
                "id" => $this->getIncomeCategory->id,
                "name" => $this->getIncomeCategory->name,
            ],
            "date" => $this->date,
            "income_reason" => $this->income_reason,
            "total_amount" => $this->total_amount,
            // 'document' => config('app.url') . "/" . config('imagepath.expense') . "/" . $this->document,
        ];
    }
}