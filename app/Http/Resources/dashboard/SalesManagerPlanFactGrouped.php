<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesManagerPlanFactGrouped extends JsonResource
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
            "productCount" => $this->KOL_VO_TOVAROV,
            "planCash" => $this->PLAN_SUMMA,
            "planCount" => $this->PLAN_KOL_VO,
            "completedCash" => $this->FACT_SUMMA,
            "completedCount" => $this->FACT_KOL_VO,
            "shippedCash" => $this->SUMMA_OTGRUZHENO,
            "shippedCount" => $this->KOL_VO_OTGRUZHENO,
            "executionPlan" => $this->ISPLONENIA_PLANA
        ];
    }
}
