<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\sprTypeVisit;

class clientsRFavoriteList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $queryTypeVisit = DB::table("CRM_SPR_TYPE_VISIT")
        ->select("ID as id", "NAME as name")
        ->get();
        $queryTypeMeeting = DB::table("CRM_SPR_TYPE_MEETING")
        ->select("ID", "NAME")
        ->get();
        

        return [
            "id" => (int)$this->ID,
            "meetingType" => sprTypeVisit::collection($queryTypeVisit)->all(),
            "meetingPlace" => sprTypeMeeting::collection($queryTypeMeeting)->all(),
            "clientId" =>  (int)$this->CLIENT_ID,
            "clientName" => $this->NAME,
            "clientIin" => $this->IIN_BIN,
            "clientAddress" =>  $this->ADDRESS
        ];
    }
}
