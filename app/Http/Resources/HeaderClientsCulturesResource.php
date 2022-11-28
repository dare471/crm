<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HeaderClientsCulturesResource extends JsonResource
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
            "type" => $this->type,
            "area" => $this->area,
            "countLand" => $this->countLand,
            "name" => $this->name,
            "address" => $this->address
        ];
    }
}
