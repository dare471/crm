<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class contactInf extends JsonResource
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
            "position" => $this->POSITION,
            "name" => $this->NAME,
            "phNumber" => $this->PHONE_NUMBER,
            "email" => $this->EMAIL
        ];
    }
}
