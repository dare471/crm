<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class clientInf extends JsonResource
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
            "clientAddress" => $this->ADDRESS,
            "clientIin" => (int)$this->IIN_BIN,
            "clientCato" => (int)$this->CATO,
            "clientAction" => $this->DEYATELNOST,
            "contactName" => $this->contactName,
            "contactPosition" => $this->POSITION,
            "contactPhone" => $this->PHONE_NUMBER,
            "contactEmail" => $this->EMAIL
        ];
    }
}
