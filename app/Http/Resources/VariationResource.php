<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'id'                           => $this->id,
            'name'                         => $this->name,
            'sub_sku'                      => $this->sub_sku,
            'image'                        => config('app.url')."/". config('imagepath.product_variation').$this->images,
            'product_barcode'              => $this->product_barcode,
            'default_purchase_price'       => $this->default_purchase_price,
            // 'dpp_inc_tax'                  => $this->dpp_inc_tax,
            'profit_percent'               => $this->profit_percent,
            'default_sell_price'           => $this->default_sell_price,
            // 'sell_price_inc_tax'           => $this->sell_price_inc_tax,
            'stock_amount'                 => $this->stock_amount,
            'alert_quantity'               => $this->alert_quantity,
            'variation_template_value'     => new VariationTemplateValueResource($this->variationValueTemplate),
            'brand'                        => new BrandResource($this->getBrand),
            'category'                     => new CategoryResource($this->getCategory),
        ];
    }
}
