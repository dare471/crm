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
            'type' => 'clientLand',
            'id' => $this->id,
            'fields' => $this->fields,
            'guid' => $this->guid,
            'geometry_rings' => json_decode($this->geometry_rings, true),
            'area' => $this->area/10000
        ];
    }
}
