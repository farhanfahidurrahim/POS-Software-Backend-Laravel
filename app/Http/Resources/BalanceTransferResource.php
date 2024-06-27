<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceTransferResource extends JsonResource
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
            "branch" => new BranchResource($this->getBranch),
            "transfer_amount" => $this->transfer_amount,
            "note" => $this->note,
            "payment_method" => $this->payment_method,
            "document" =>config('app.url')."/". config('imagepath.balancetransfer')."/".$this->document,
            "created_at" => $this->created_at->format('d M y, g:i:s A'),
        ];
    }
}
