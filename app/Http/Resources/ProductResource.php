<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'sku'               => $this->sku,
            'unit_id'           => $this->unit_id,
            'sub_unit_ids'      => $this->sub_unit_ids,
            'category_id'       => $this->category_id,
            'sub_category_id'   => $this->sub_category_id,
            'status'            => $this->status,
            'detail'            => $this->detail,
            'image'             => config('app.url')."/". config('imagepath.product').$this->image,
            'type'              => $this->type,
            'variation'         => VariationResource::collection($this->variations),
            // 'variation'      => VariationResource::collection($this->whenLoaded('variations')),
            // 'variations'     => ProductVariationResource::collection($this->whenLoaded('productVariations')),
            'category'          => new CategoryResource($this->category),
            'brand'             => new BrandResource($this->brand),
            'unit'              => new UnitResource($this->unit),
            'created_by'        => $this->user->name,
        ];
    }
}
