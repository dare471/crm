<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class contractRelation extends JsonResource
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
            "contractName" => $this->NAIMENOVANIE,
            "contractStart" => $this->DATA_NACHALA_DEYSTVIYA,
            "contractEnd" => $this->DATA_OKONCHANIYA_DEYSTVIYA,
            "contractNumber" => $this->NOMER,
            "contractStatus"=> $this->STATUS,
            "contractClientName" => $this->KONTRAGENT,
            "contractManager" => $this->MENEDZHER,
            "contractSeason" => $this->SEZON,
            "contrDeliveryAddress" => $this->ADRES_DOSTAVKI,
            "contractSumm" => (int)$this->SUMMA_KZ_TG
        ];
    }
}
