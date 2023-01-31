<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class regionBelong extends JsonResource
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
            "id" => $this->ID,
            "name" => $this->NAME,
            "regionId" => $this->KATO
        ];
    }
}
