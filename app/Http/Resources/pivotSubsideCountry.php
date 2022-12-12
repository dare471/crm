<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class pivotSubsideCountry extends JsonResource
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
            'type' => "pivotSubsideCountry",
            'category' => $this->CATEGORY,
            'season' => $this->SEASON,
            'cash' => $this->CASH,
            'area' => $this->AREA,
            'count' => $this->COUNT,
            'factCount' => $this->FACT_COUNT
        ];
    }
}
