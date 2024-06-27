<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'short_name'   => $this->short_name,
            'base_unit_id' => $this->base_unit_id,
            // 'allow_decimal'=> $this->allow_decimal,
            'children'     => UnitResource::collection($this->whenLoaded('children')),
            'parent'       => $this->parent,
            'base_unit_multiplier'=> $this->base_unit_multiplier,
            'created_by'   => $this->getAuthUser->name,
        ];
    }
}
