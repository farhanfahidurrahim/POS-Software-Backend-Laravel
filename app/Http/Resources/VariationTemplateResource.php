<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationTemplateResource extends JsonResource
{
   
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'branch_id'    => new BranchResource($this->branch),
            // 'value'        => VariationTemplateValueResource::collection($this->whenLoaded('values')),
            'values'        => VariationTemplateValueResource::collection($this->values),
        ];
    }
}
