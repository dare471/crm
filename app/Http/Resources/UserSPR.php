<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSPR extends JsonResource
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
            "guid" => $this->guid,
            "id" => (int)$this->ID,
            "telegramId" => (int)$this->TELEGRAM_ID,
            "fullName" => $this->NAIMENOVANIE,
            "direction" => $this->DIREKTSYA, 
            "position" => $this->DOLZHNOST,
            "email" => $this->ADRES_E_P,
            "phone" => $this->TELEFON,
            "subDivision" => $this->PODRAZDELENIE,
            "crmCato" => $this->CRM_CATO
        ];
    }
}
