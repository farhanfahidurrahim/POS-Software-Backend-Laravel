<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            // 'designation' => new DesignationResource($this->designation),
            'image' =>config('app.url')."/". config('imagepath.employee').$this->image,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'user_type' => $this->user_type,
            // 'family_number' => $this->family_number,
            // 'nid' => $this->nid,
            // 'passport' => $this->passport,
            // 'dob' => $this->dob,
            // 'gender' => $this->gender,
            // 'marital_status' => $this->marital_status,
            // 'blood_group' => $this->blood_group,
            // 'bank_details' => $this->bank_details,
            // 'current_address' => $this->current_address,
            // 'permanent_address' => $this->permanent_address,
            // 'status' => $this->status,
        ];
    }
}
