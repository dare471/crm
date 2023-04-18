<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetClientXL extends JsonResource
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
            "Row" => (int) $this->Row,
            "clientName" => $this->clientName,
            "sumClient" => (int)$this->sumClient,
            "clientIin" => (int)$this->clientIin,
            "clientAddress" => $this->clientAddress
        ];
    }
}
