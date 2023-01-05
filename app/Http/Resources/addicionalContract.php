<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class addicionalContract extends JsonResource
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
            "contractGuid" => $this->GUID,
            "contractName" => $this->NAIMENOVANIE,
            "mainContract" => $this->main 
        ];
    }
}
