<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ElevatorListResource extends JsonResource
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
            'id' => $this->ID,
            'name' => $this->NAME,
            'bin' => $this->BIN,
            'location' => $this->LOCATION,
            'station' => $this->STATION,
            'contacts' => $this->CONTACTS,
            'storageVolume' => $this->STORAGE_VOLUME,
            'latitude' => $this->LATITUDE,
            'longitude' => $this->LONGITUDE
        ];
    }
}
