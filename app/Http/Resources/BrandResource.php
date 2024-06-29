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
            'category'     => [
                'id' => $this->getCategory->id ?? "--",
                'name' => $this->getCategory->name ?? "--",
            ],
            'description'  => $this->description,
            'created_by'   => $this->getAuthUser->name,
        ];
    }
}