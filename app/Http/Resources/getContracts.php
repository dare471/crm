<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getContracts extends JsonResource
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
            "dateCreate" => $this->DATA,
            "dateStart" => $this->DATA_NACHALA_DEYSTVIYA,
            "dateEnd" => $this->DATA_OKONCHANIYA_DEYSTVIYA,
            "number" => $this->NOMER,
            "succes" => $this->SOGLASOVAN,
            "status" => $this->STATUS,
            "managerContract" => $this->MENEDZHER,
            "season" => $this->SEZON,
            "conditionPay" => $this->USLOVIYA_OPLATY,
            "deliveryMethod" => $this->SPOSOB_DOSTAVKI,
            "sum" => $this->SUMMA_KZ_TG,
            "additionalContract" => $this->NOMER_DOP_SOGLASHENIYA,
            "mainContract" => $this->OSNOVNOY_DOGOVOR_ID
        ];
    }
}
