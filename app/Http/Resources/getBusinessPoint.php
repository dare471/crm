<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getBusinessPoint extends JsonResource
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
            "id" => (int)$this->ID,
            "clientId" => (int)$this->CLIENT_ID,
            "category" => $this->NAME_C,
            "name" => $this->NAME,
            "coordinate" => json_decode($this->COORDINATE) 
        ];
    }
}
