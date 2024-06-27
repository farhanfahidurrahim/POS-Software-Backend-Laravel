<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Variation;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $variationIds = json_decode($this->variation_id);
        $variations = Variation::whereIn('id', $variationIds)->get();

        $sortedVariations = $variations->sortBy(function ($variation) use ($variationIds) {
            return array_search($variation->id, $variationIds);
        })->values();

        $cancel_sale = ($this->status == 'Cancel') ? 1 : 0;
        $cancel_sale_amount = 0;
        if ($cancel_sale == 1) {
            $cancel_sale_amount = $this->total_amount;
        }

        return [
            "id" => $this->id,
            "invoice" => $this->invoice,
            //"barcode_path" => $this->barcode_path,
            'barcode_path' => config('app.url') . "/" . $this->barcode_path,
            'customer' => new CustomerResource($this->getCustomer),
            "variation_id" => VariationResource::collection($sortedVariations),
            "quantity" => json_decode($this->quantity),
            "total_qty" => array_sum(json_decode($this->quantity)),
            "unit_price" => json_decode($this->unit_price),
            "discount_amount" => json_decode($this->discount_amount),
            "sub_totals" => json_decode($this->sub_totals),
            "discount_type_subtotal" => $this->discount_type_subtotal,
            "discount_on_subtotal_amount" => $this->discount_on_subtotal_amount,
            'shipping_charge' => $this->shipping_charge,
            'courier_id' => new CourierShippingResource($this->getCourier),
            'delivery_method' => $this->delivery_method,
            'total_amount' => $this->total_amount,
            'bkash' => $this->bkash,
            'bkash_number' => $this->bkash_number,
            'nagad' => $this->nagad,
            'nagad_number' => $this->nagad_number,
            'rocket' => $this->rocket,
            'rocket_number' => $this->rocket_number,
            'bank' => $this->bank,
            'bank_number' => $this->bank_number,
            'cheque' => $this->cheque,
            'cheque_number' => $this->cheque_number,
            'cash' => $this->cash,
            'cash_number' => $this->cash_number,
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,
            'payment_status' => $this->payment_status,
            'sale_from' => $this->sale_from,
            'dispatch_status' => $this->dispatch_status == 1 ? "Yes" :  "No",
            'dispatch_date' => $this->dispatch_date ? Carbon::parse($this->dispatch_date)->format('d-M-Y') : "",
            'note' => $this->note,
            'status' => $this->status,
            'sale_return' => $this->sale_return,
            'created_by' => $this->getUser->name,
            'created_at' => $this->created_at->format('d M y, g:i:s A'),
            "cancel_sale" => $cancel_sale,
            "cancel_sale_amount" => $cancel_sale_amount,
        ];
    }
}
