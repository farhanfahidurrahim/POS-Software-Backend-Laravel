<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'module_name' => $this->module_name,
        ];
    }
}
