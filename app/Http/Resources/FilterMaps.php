<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilterMaps extends JsonResource
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
            'type'=> $this->type,
            'fieldsID' => $this->fieldsID,
            'clientID' => $this->clientID,
            'clientName' => $this->clientName,
            'clientBin' => $this->IIN_BIN,
            'guid' => $this->guid,
            'cultureID' => $this->cultureID,
            'cultureName' => $this->cultureName,
            'district' => $this->district,
            'region' => $this->region,
            'geometry_ring' => json_decode($this->geometry_rings, true), 
        ];
    }
}
