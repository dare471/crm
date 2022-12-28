<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function PHPSTORM_META\map;

class managerContract extends JsonResource
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
            "contractGuid" => (binary)$this->CONTRACTS_GUID,
            "managerId" => (int)$this->ID,
            "managerName" => $this->managerName,
            "managerPosition" => $this->DOLZHNOST,
            "contractName" => $this->NAIMENOVANIE,
            "contractDirection" => $this->DIREKTSYA,
            "contractClientId" => (int)$this->clientId,
            "contractClientName" => $this->KONTRAGENT,
            "contractClientIin" => (int)$this->IIN_BIN,
            "contractSeason" => $this->SEZON,
            "contractConditionPay" => $this->USLOVIYA_OPLATY,
            "contractTypeDelivery" => $this->SPOSOB_DOSTAVKI,
            "contractDeliveryAddress" => $this->ADRES_DOSTAVKI,
            "contractSumm" => (int)$this->SUMMA_KZ_TG
        ];
    }
}
