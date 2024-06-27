<?php

namespace App\Http\Resources;

use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\API\PathaoCourierApiController;

class CustomerResource extends JsonResource
{
    // public function toArray($request)
    // {
    //     $pathao = new PathaoCourierApiController;
    //     $cityResult = $pathao->getCities();
    //     $cities = $cityResult->data->data;

    //     $selectedCity = null;
    //     foreach ($cities as $city) {
    //         if ($city->city_id == $this->city_id) {
    //             $selectedCity = $city;
    //             break;
    //         }
    //     }

    //     $city_name = null;
    //     $zone_name = null;
    //     $area_name = null;

    //     if ($selectedCity) {
    //         $zoneResult = $pathao->getZones($selectedCity->city_id);
    //         $zones = $zoneResult->data->data;

    //         foreach ($zones as $zone) {
    //             if ($zone->zone_id == $this->zone_id) {
    //                 $zone_name = $zone->zone_name;
    //                 break;
    //             }
    //         }

    //         $city_name = $selectedCity->city_name;
    //     }

    //     if ($zone_name) {
    //         $areaResult = $pathao->getAreas($zone->zone_id);
    //         $areas = $areaResult->data->data;

    //         foreach ($areas as $area) {
    //             if ($area->area_id == $this->area_id) {
    //                 $area_name = $area->area_name;
    //                 break;
    //             }
    //         }
    //     }

    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'email' => $this->email,
    //         'phone_number' => $this->phone_number,
    //         'city_id' => $this->city_id,
    //         'city_name' => $city_name,
    //         'zone_id' => $this->zone_id,
    //         'zone_name' => $zone_name,
    //         'area_id' => $this->area_id,
    //         'area_name' => $area_name,
    //         'location' => $this->location,
    //     ];
    // }

    ///////////////// With Request city,zone,area
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'location' => $this->location,
            // 'city_id' => $this->city_id,
            // 'city_name' => $this->city_name,
            // 'zone_id' => $this->zone_id,
            // 'zone_name' => $this->zone_name,
            // 'area_id' => $this->area_id,
            // 'area_name' => $this->area_name,
        ];
    }
}