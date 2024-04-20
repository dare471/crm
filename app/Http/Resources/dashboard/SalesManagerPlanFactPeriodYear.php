<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesManagerPlanFactPeriodYear extends JsonResource
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
            "name" => $this->MANAGER,
            "month" => $this->MONTH_NAME,
            "completedCash21" => $this->SUMMA_21,
            "executionPlanPercent21" => $this->ISPOLNENIE_PLANA_21,
            "completedCash22" => $this->SUMMA_22,
            "executionPlanPercent22" => $this->ISPOLNENIE_PLANA_22,
            "completedCash23" => $this->SUMMA_23,
            "executionPlanPercent23" => $this->ISPOLNENIE_PLANA_23,
            "completedCash23" => $this->SUMMA_24,
            "executionPlanPercent23" => $this->ISPOLNENIE_PLANA_24,
        ];
    }
}
