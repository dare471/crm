<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
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
                "clientId" => (int)$this->ID,
                "clientName" => $this->NAME,
                "clientIin" => (int)$this->IIN_BIN,
                "clientAddress" => $this->ADDRESS,
                "visitName" => $this->visitName,
                "visitId" => (int)$this->visitId,
                "meetingId" => (int)$this->meetingId,
                "meetingTime" => $this->timeMeeting,
                "meetingName" => $this->meetingName,
                "plotId" => (int)$this->plotId   
        ];
    }
}
