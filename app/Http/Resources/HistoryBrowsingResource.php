<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryBrowsingResource extends JsonResource
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
            'id' => (int)$this->ID,
            'userId' => (int)$this->USER_ID,
            'regionName' => $this->ADDRESS,
            'regionId'=> (int)$this->REGION,
            'clientId' => (int)$this->CID,
            'clientName' => $this->NAME,
            'clientPlotName' => $this->FIELDS,
            'clientPlotId' => (int)$this->CLIENT_FIELDS
        ];
    }
}
