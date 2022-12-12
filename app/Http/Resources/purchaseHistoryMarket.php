<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class purchaseHistoryMarket extends JsonResource
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
            'type' => 'purchaseHistoryMarket',
            'clientIin' => $this->CORRECT_APPLICANT_IIN_BIN,
            'culture' => $this->CULTURE,
            'season' => $this->YEAR,
            'sumSubsidies' => $this->SUM_SUBSIDIES 
        ];
    }
}
