<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'category'     => new CategoryResource($this->getCategory),
            'description'  => $this->description,
            'created_by'   => $this->getAuthUser->name,
        ];
    }
}
