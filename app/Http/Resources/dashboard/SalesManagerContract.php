<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesManagerContract extends JsonResource
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
            "client" => $this->KONTRAGENT,
            "product" => $this->NOMENKLATURA,
            "contractNumber" => $this->SUMMA_21,
            "cashKzTg" => $this->SUMMA_KZ_TG,
            "productCount" => $this->KOL_VO,
            "shippedCash" => $this->SUMMA_OTGRUZHENO,
            "shippedCount" => $this->KOL_VO_OTGRUZHENO,
            "productPaid" => $this->OPLACHENO_PO_TOVARAM
        ];
    }
}
