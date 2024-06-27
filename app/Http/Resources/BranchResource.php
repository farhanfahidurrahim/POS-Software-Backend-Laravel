<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'                       => $this->id,
            'name'                     => $this->name,
            'division'                 => new DivisionResource($this->division),
            'district_id'              => new DistrictResource($this->district),
            'upazila_id'               => new UpazilaResource($this->upazila),
            'city'                     => $this->city,
            'state'                    => $this->state,
            'post_code'                => $this->post_code,
            'address'                  => $this->address,
            'phone_number'             => $this->phone_number,
            'alternate_number'         => $this->alternate_number,
            'email'                    => $this->email,
            'website'                  => $this->website,
            'default_payment_accounts' => $this->default_payment_accounts,
            'status'                   => $this->status,
            'balance'                  => $this->balance,
            'created_at'               => Carbon::parse($this->created_at)->format('d M Y'),
        ];
    }
}
