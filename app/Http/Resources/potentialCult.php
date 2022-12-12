<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class potentialCult extends JsonResource
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
            'type' => 'potentialCult',
            'clientIinbin' =>  $this->IIN_BIN,
            'culture' =>  $this->CULTURE,
            'cash' => $this->CASH,
            'area' => $this->AREA
        ];
    }
}
