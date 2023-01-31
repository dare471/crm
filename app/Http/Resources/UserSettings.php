<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSettings extends JsonResource
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
            "clientId" => (int)$this->ID,
            "clientName" => $this->NAME,
            "clientAddress" => $this->ADDRESS,
            "clientIinBin" => (int)$this->IIN_BIN,
            "clientType" => $this->DEYATELNOST
        ];
    }
}
