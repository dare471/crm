<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CalculatorKPResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        return /* parent::toArray($request); */ [
            'id' =>  $this->ID,
            'type' => $this->type,
            'guid' => $this->guid,
            'name' => $this->name,
            'client_info_id' => $this->client_info_id,
            'geometry_rings' => json_decode($this->geometry_rings, true)
        ];
    }
}


