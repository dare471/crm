<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictPolygonResource extends JsonResource
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
            'id' =>  (int)$this->district,
            'type' => $this->type,
            'name' => $this->TEXT,
            'klkod' => $this->KLKOD,
            'vnaim' => $this->VNAIM,
            'geometry_rings' => json_decode($this->geometry_rings, true),
        ];
    }
}
