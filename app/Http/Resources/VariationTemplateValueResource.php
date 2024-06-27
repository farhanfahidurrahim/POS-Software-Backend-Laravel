<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationTemplateValueResource extends JsonResource
{
   
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'value'        => $this->name,
            'templateValue_id' => new VariationTemplateValueResource($this->templateValue),
        ];
    }
}
