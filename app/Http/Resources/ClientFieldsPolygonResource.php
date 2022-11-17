<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientFieldsPolygonResource extends JsonResource
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
            'type' => $this->type,
            'id' =>  $this->ID,
            'guid' => $this->guid,
            'name' => $this->name,
            'client_info_id' =>  $this->client_info_id,
            'geo' => json_decode($this->geometry_rings, true),
        ];
    }
}
