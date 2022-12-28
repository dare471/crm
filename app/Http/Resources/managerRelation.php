<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class managerRelation extends JsonResource
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
            "managerId" => $this->ID,
            "managerName" => $this->NAIMENOVANIE,
            "managerPosition" => $this->DOLZHNOST,
            "seasonRelation" => $this->SEZON
        ];
    }
}
