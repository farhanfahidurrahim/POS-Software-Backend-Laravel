<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationPosResource extends JsonResource
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
            'id'                       => $this->id,
            'name'                     => $this->name,
            'image'                    => config('app.url')."/". config('imagepath.product_variation').$this->images,
            'default_sell_price'       => $this->default_sell_price,
            'stock_amount'             => $this->stock_amount,
            'alert_quantity'           => $this->alert_quantity,
            'variation_template_value' => $this->variationValueTemplate->name,
            'brand'                    => $this->getBrand ? [
                                            'id'   => $this->getBrand->id,
                                            'name' => $this->getBrand->name,
                                        ] : null,
            'category'                  => [
                                            'id'   => $this->getCategory->id,
                                            'name' => $this->getCategory->name,
                                        ],
        ];
    }
}
