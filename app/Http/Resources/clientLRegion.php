<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class clientLRegion extends JsonResource
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
            "clientId" =>  (int)$this->clientId,
            "clientName" => $this->NAME,
            "clientAddress" => $this->ADDRESS,
            "clientIin" => (int)$this->IIN_BIN,
            "clientCato"=> (int)$this->CATO
        ];
    }
}
