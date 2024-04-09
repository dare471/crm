<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getLastPlanPayment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "ID" => $this->resource['ID'],
            "documentGuid" => (string) $this->resource['documentGuid'],
            "payment" => $this->resource['payment'],
            "sheduledAmount" => number_format($this->resource['sheduledAmount'], '0', '.', ' ')." ₸",
            "remainder" => number_format($this->resource['remainder'], '0', '.', ' ')." ₸",
            "docNumber" => (string) $this->resource['docNumber'],
            "season" => (string) $this->resource['season'],
            "dateOnSchedule" => (string) $this->resource['dateOnSchedule'],
            "managerName" => (string) $this->resource['managerName'],
            "client" => (string) $this->resource['client'],
            "clientId" => (string) $this->resource['clientId'],
            "weekNumber" => (string) $this->resource['weekNumber'],

        ];
    }
}
