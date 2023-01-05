<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getAnalytic extends JsonResource
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
            "year" => (int)$this->YEAR,
            "regionName" => $this->REGION,
            "regionId" => (int)$this->KATO,
            "clientProcAA" => (int)$this->AA_CLIENTS_PROC,
            "clientProcOth" => (int)$this->OTHER_CLIENTS_PROC,
            "clientCountAll" => (int)$this->CLIENTS_KZ,
            "clientCountAA" => (int)$this->CLIENTS_AA,
            "clientCountOth" => (int)$this->CLIENTS_OTHER,
            "clientAreaAAProc" => (int)$this->AA_AREA_PROC,
            "clientAreaOthProc" => (int)$this->OTHER_AREA_PROC,
            "clientAreaAll" => (int)$this->AREA_CLIENTS_KZ,
            "clientAreaAA" => (int)$this->AREA_CLIENTS_AA,
            "clientAreaOth" => (int)$this->AREA_CLIENTS_OTHER
        ];
    }
}
