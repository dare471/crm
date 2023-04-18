<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
class ProfieResource extends JsonResource
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
            "mngId" => (int)$this->MANAGER_ID,
            "level" => (int)$this->GRADE,
            "sumContracts" => (float)$this->SUM_DOGOVOR,
            "sumContractsString" => number_format($this->SUM_DOGOVOR, 2, '.', " "). " ₸",
            "sumPlan" => (float)$this->SUM_PLAN,
            "sumPlanString" => number_format($this->SUM_PLAN, 2, '.', " "). " ₸",
            "margin" => round((float)$this->MARGIN * 100),
            "sumMargin" => number_format($this->SUM_MARGINALITY,  0, '.', " "). " ₸",
            "planMargin" => number_format((float)$this->PLAN_MARGINALITY, 0, '.', " "). " ₸",
            "planStep" => number_format(round((float)$this->PLAN * 100), 0, '.', " "). " ₸",
            "step" => (int)$this->STEP,
            "coefficientPlanned" => (float)$this->PLANNED,
            "coefficientUnplanned" => (float)$this->UNPLANNED,
            "basis" => number_format(round((float)$this->BASIS), 0, '.', ' '). " ₸"
        ];
    }
}
