<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class areaStructure extends JsonResource
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
            'type' => 'areaStructure',
            'clientIinbin' => $this->OWNER_IIN_BIN,
            'culture' =>  $this->CULTURE,
            'season' => $this->YEAR,
            'area' => $this->AREA
        ];
    }
}
