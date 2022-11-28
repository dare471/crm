<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetClientLandsResource extends JsonResource
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
            "type" => $this->type,
            "landId" => $this->ID,
            "guid" => $this->guid,
            "clientName" =>  $this->name,
            "clientId" => $this->clientID,
            "geometry_rings" => json_decode($this->geometry_rings)
        ];
    }
}
