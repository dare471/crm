<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Boolean;

class plannedMeeting extends JsonResource
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
                "visitId" => (int)$this->visitId,
                "statusVisit" => (Boolean)$this->statusVisit,
                "clientId" => (int)$this->ID,
                "clientName" => $this->NAME,
                "dateVisit" => $this->dateVisit,
                "clientIin" => (int)$this->IIN_BIN,
                "clientAddress" => $this->ADDRESS,
                "visitTypeName" => $this->visitTypeName,
                "visitTypeId" => (int)$this->visitTypeId,
                "meetingTypeId" => (int)$this->meetingTypeId,
                "meetingTime" => $this->timeMeeting,
                "meetingTypeName" => $this->meetingTypeName,
                "plotId" => (int)$this->plotId   
        ];
    }
}
