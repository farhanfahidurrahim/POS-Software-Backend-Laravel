<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_logo' => config('app.url')."/". config('imagepath.setting')."/".$this->company_logo,
            'company_name' => $this->company_name,
            'company_email' => $this->company_email,
            'company_phone' => $this->company_phone,
            'company_address' => $this->company_address,
            'charge_out_dhaka' => $this->charge_out_dhaka,
            'charge_in_dhaka' => $this->charge_in_dhaka,
            'currency_symbol' => $this->currency_symbol,
        ];
    }
}
