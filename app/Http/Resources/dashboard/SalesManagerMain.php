<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesManagerMain extends JsonResource
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
            "id" => (int)$this->ID,
            "name" => $this->MANAGER,
            "provider" => $this->POSTAVCHIK,
            "product" => $this->NOMENKLATURA,
            "planCash" => (float)$this->PLAN_SUMMA,
            "planCount" => (float)$this->PLAN_KOL_VO,
            "completedCash" => (float)$this->FACT_SUMMA,
            "completedCount" => (float)$this->FACT_KOL_VO,
            "shippedCash" => (float)$this->SUMMA_OTGRUZHENO,
            "shippedCount" => (float)$this->KOL_VO_OTGRUZHENO,
            "executionPlan" => (float)$this->ISPLONENIA_PLANA
        ];
    }
}
