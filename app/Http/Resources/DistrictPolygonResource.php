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
            'type' => $this->type,
            'name' => $this->TEXT,
            'klkod' => $this->KLKOD,
            'vnaim' => $this->VNAIM,
            'cato' =>  $this->cato,
            'geometry_rings' => json_decode($this->geometry_rings, true),
        ];
    }
}
