<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegionPolygonResource extends JsonResource
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
            'id' =>  $this->ID,
            'type' => $this->type,
            'name' => $this->NAME,
            'cato' => $this->cato,
            'population_area' => $this->population_area,
            'geometry_rings' => json_decode($this->geometry_rings, true)
        ];
    }
}
