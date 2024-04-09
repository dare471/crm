<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlannedMeetingMobileList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request) { 
        return [ 
            "id" => (int)$this->ID, 
            "clientId" => (int)$this->CLIENT_ID, 
            "statusVisit" => (boolean)$this->STATUS, 
            "source" => $this->SOURCE, 
            "dateVisitStamp" => Carbon::parse($this->DATE_TO_VISIT)->timestamp,
            "dateVisit" => Carbon::parse($this->DATE_TO_VISIT)->toDateString(),  
            "targetDescription" => $this->TARGET_DESCRIPTION,
            "properties" => $this->propsMeeting($this->ID) 
        ]; 
    }
    private function propsMeeting($id) { 
        $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES as cvtdp")
        ->where("VISIT_ID", $id) 
        ->first(); 
        return $query ? collect([
            "statusVisit" => (boolean)$this->STATUS, 
            "id" => (int)$query->ID, 
            "visitId" => (int)$query->VISIT_ID,
            "clientId" =>(int)$this->CLIENT_ID,
            "clientName" => $this->clientInf($this->CLIENT_ID)->NAME,
            "clientAddres" => $this->clientInf($this->CLIENT_ID)->ADDRESS,
            "typeVisitId" => (int)$query->TYPE_VISIT_ID,
            "typeVisitName" => $this->getHanndBookTypeVisit($query->TYPE_VISIT_ID),
            "typeMeetingId" => (int)$query->TYPE_MEETING,
            "typeMeetingName" => $this->getHanndBookTypeMeeting($query->TYPE_MEETING),
            "startVisit" => $query->STARTVISIT ? Carbon::parse($query->STARTVISIT)->timestamp  : "Данные не указаны", 
            "finishVisit" => $query->FINISHVISIT ? Carbon::parse($query->FINISHVISIT)->timestamp  : "Данные не указаны",
            "mainDescription" => $query->DESCRIPTION,
            "placeDescription" => $query->PLACE_DESCRIPTION 
            ]) : null; 
    }
    private function clientInf($id){
        $query = DB::table("CRM_CLIENT_INFO")
        ->where("ID", $id)
        ->select("NAME", "ADDRESS")
        ->first();
        return $query;
    }
    private function getHanndBookTypeVisit($id){
        $query = DB::table("CRM_SPR_TYPE_VISIT")
        ->where("ID", $id)
        ->value("NAME");
        return $query;
    }
    private function getHanndBookTypeMeeting($id){
        $query = DB::table("CRM_SPR_TYPE_MEETING")
        ->where("ID", $id)
        ->value("NAME");
        return $query;
    }
}
