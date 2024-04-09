<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDashboard extends JsonResource
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
            "userId" => $this->ID,
            "userName" => $this->MANAGER,
            "countProduct" => $this->KOL_VO_TOVAROV,
            "planSum" => number_format($this->PLAN_SUMMA, 2, '.', ' '), 
            "planCountProduct" => number_format($this->PLAN_KOL_VO, 2, '.', ' '),
            "factCountProduct" => number_format($this->FACT_KOL_VO, 2, '.', ' '),
            "sumShipment" => number_format($this->SUMMA_OTGRUZHENO, 2, '.', ' '),
            "countShipment" =>number_format($this->KOL_VO_OTGRUZHENO, 2, '.', ' '),
            "executionPlan" => $this->ISPLONENIA_PLANA * 100
        ];
    }
}
