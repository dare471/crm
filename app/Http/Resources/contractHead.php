<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class contractHead extends JsonResource
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
            "contractGuid" =>  (binary)$this->CONTRACTS_GUID,
            "managerId" => (int)$this->ID,
            "managerName" =>  $this->NAIMENOVANIE,
            "managerDirection" => $this->DIREKTSYA,
            "managerPosition" =>  $this->DOLZHNOST,
            "clientName" => $this->KONTRAGENT,
            "contractName" => $this->contractName,
            "contractSeason" => $this->SEZON,
            "conditionPay" => $this->USLOVIYA_OPLATY,
            "deliveryMethod" => $this->SPOSOB_DOSTAVKI,
            "deliveryAddress" => $this->ADRES_DOSTAVKI,
            "contractSum" => $this->SUMMA_KZ_TG,
            "additionalAgreement" => $this->NOMER_DOP_SOGLASHENIYA,
            "mainContract" => $this->MAIN_CONTRACTS
        ];
    }
}
