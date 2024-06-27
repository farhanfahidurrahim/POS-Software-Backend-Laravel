<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            // 'id' => $this->id,
            // 'name' => $this->name,
            // // 'variations' => VariationResource::collection($this->whenLoaded('variations')),
            // 'product_variation' => VariationResource::collection($this->variations),
        ];
    }
}
