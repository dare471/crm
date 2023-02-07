<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getLastContract extends JsonResource
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
            "name" => $this->NAIMENOVANIE,
            "number" => $this->NOMER,
            "dateStart" => $this->DATA_NACHALA_DEYSTVIYA,
            "dateEnd" => $this->DATA_OKONCHANIYA_DEYSTVIYA,
            "status" => $this->STATUS,
            "client" => $this->KONTRAGENT,
            "manager" => $this->manager,
            "season" => $this->SEZON,
            "deliveryAddress" => $this->ADRES_DOSTAVKI,
            "sum" => $this->SUMMA_KZ_TG
        ];
    }
}
