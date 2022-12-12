<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class purchaseHistory extends JsonResource
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
            'type' => 'purchaseHistory',
            'clientIinbin' => $this->CORRECT_APPLICANT_IIN_BIN,
            'culture' =>  $this->CULTURE,
            'season' => $this->YEAR,
            'cash' => $this->SUM_SUBSIDIES
        ];
    }
}
