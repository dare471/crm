<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class pivotYieldStructure extends JsonResource
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
            'type' => 'pivotYieldStructure',
            'clientIinbin' => $this->OWNER_IIN_BIN,
            'culture' => $this->CULTURE,
            'season' => $this->YEAR,
            'cropCapacity' => $this->CROP_CAPACITY
        ];
    }
}
