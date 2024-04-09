<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Support\Facades\DB;

class PlannedMettingDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request){
        return [
            "visitId" => (int)$this->visitId,
            "clientId" => (int)$this->clientId,
            "clientName" => $this->clientName,
            "clientCategory" => trim($this->buisnessCategory, ' '),
            "address" => $this->ADDRESS,
            "contactInf" => $this->contactInf($this->clientId),
            "clientIin" => (int)$this->IIN_BIN,
            "managerId" => (int)$this->managerId,
            "managerName" => $this->managerLinkClient($this->managerId),
            "startVisit" => $this->startVisit,
            "finishVisit" => $this->finishVisit,
            "statusVisit" => (Boolean)$this->statusVisit,
            "visitTypeId" => (int)$this->visitTypeId,
            "vistiTypeName" => $this->sprVisit($this->visitTypeId),
            "meetingTypeId" => (int)$this->meetingTypeId,
            "meetingTypeName" => $this->sprMeeting((int)$this->meetingTypeId),
            "meetingCoordinate" => (string)$this->MEETING_COORDINATE,
            "plotId" => (int)$this->PLOT,
            "plotName" => $this->plotSpr($this->PLOT),
            "summContract" => $this->sumContractsAll($this->clientId). " ₸",
            "summCurrentContractSeason" => $this->classificationSum($this->sumCurrentSeasonContracts($this->clientId)). " ₸",
            "checkContracts" => (boolean)$this->contractList($this->clientId),
            "potentialClientPercent" => round($this->potentialClient($this->clientId, $this->sumCurrentSeasonContracts($this->clientId)))."%",
            "potentialClient" => $this->potentialClient($this->clientId, null),
            "subscidesSum" => $this->subcidesSum($this->IIN_BIN)." ₸",
            "checkSubscides" => (boolean)$this->subcidesAll($this->IIN_BIN),
            "duration" => $this->DURATION,
            "distance" => $this->DISTANCE
        ];
    }
    private function contactInf($id){
        $contact = DB::table("CRM_CLIENT_CONTACTS")
        ->where("CLIENT_ID", $id)
        ->whereNotNull("PHONE_NUMBER")
        ->get();
        $collect = $contact->map(function($item) {
            return collect($item)->filter(function($value, $key) {
                return $key !== 'PHONE_NUMBER' || $value !== null;
            })->toArray();
        });
        
        return contactInf::collection($contact)->all();
    }
    private function subcidesAll($iin){
        $list = DB::table("CRM_SHYMBULAK_SUBSIDIES")
        ->where("CORRECT_APPLICANT_IIN_BIN", $iin)
        ->orderByDesc("YEAR")
        ->get();
        return subcidesAll::collection($list)->all();
    }
    private function subcidesSum($iin){
        $sum = DB::table("CRM_SHYMBULAK_SUBSIDIES")
        ->where("CORRECT_APPLICANT_IIN_BIN", $iin)
        ->sum("SUM_SUBSIDIES");
        return $this->classificationSum((int)$sum);
    }
    private function potentialClient($id, $sumcontract){
        if(!$sumcontract){
            $p = DB::table("CRM_CLIENT_INFO")
            ->where("ID", $id)
            ->value("potential");
            return 'Данные отсутсвуют';//$this->classificationSum((int)$p);
        }
        else{
            $p = DB::table("CRM_CLIENT_INFO")
            ->where("ID", $id)
            ->value("potential");
                if($p == null){
                    $pt = 100;
                }
                else{
                    $pt = ((int)$p / (int)$sumcontract ) * 10;
                    //$pt = $p;
                }
            return $pt;
        }
    }
    private function contractList($id){
        if(is_null($id)){
            return 0;
        }
        else{
            $list = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA as cds")
            ->select(DB::raw("SUM(CONVERT(int,KOLICHESTVO)) as KOLICHESTVO"),"NOMENKLATURA", DB::raw("AVG(CONVERT(int,TSENA)) as TSENA"))
            ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "cds.DOGOVOR_GUID")
            ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
            ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
            ->where("ccig.ID", $id)
            ->where("cd.SEZON", "Сезон 2023")
            ->groupBy("NOMENKLATURA_GUID",  "NOMENKLATURA")
            ->orderByDesc("KOLICHESTVO")
            ->limit(100)
            ->get();
            return nomenClatureGroup::collection($list)->all();
        }
    }
    private function sumContractsAll($id){
        if(is_null($id)){
            return 0;
        }
        else{
        $sum = DB::table("CRM_DOGOVOR as cd")
            ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
            ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID","ccig.ID")
            ->where("cci.ID", $id)
            ->sum("SUMMA_KZ_TG");
        return $this->classificationSum((int)$sum);
        }
    }
    private function sumCurrentSeasonContracts($id){
        if(is_null($id)){
            return 0;
        }
        else{
            $sum = DB::table("CRM_DOGOVOR as cd")
            ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
            ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID","ccig.ID")
            ->where("cci.ID", $id)
            ->where("cd.SEZON", "Сезон 2023")
            ->sum("SUMMA_KZ_TG");
            return (int)$sum;
        }
    }
    private function managerLinkClient($id){
        $q = DB::table("CRM_USERS")
        ->where("ID", $id)
        ->value("NAIMENOVANIE");
        return $q;
    }
    private function sprVisit($id){
        $spr = DB::table("CRM_SPR_TYPE_VISIT")
            ->select("NAME as name")
            ->where("ID", $id)
            ->value('name');
            return $spr;
    }
    private function sprMeeting($id){
        $spr = DB::table("CRM_SPR_TYPE_MEETING")
        ->select("NAME as name")
        ->where("ID", $id)
        ->value('name');
        return $spr;
    }
    private function plotSpr($id){
        $plotSpr = DB::table("CRM_CLIENT_PROPERTIES")
        ->where("id", $id)
        ->value("FIELDS");
        return $plotSpr;
    }
    public function classificationSum($number){
        $billions = number_format(floor($number / 1000000000), 0, '.', '');
        $millions = number_format(floor(($number % 1000000000) / 1000000), 0, '.', '');
        $thousands = number_format(($number % 1000000) / 1000, 0, '.', '');
        $parts = array($billions, $millions, $thousands);
        if($parts[0] == 0){
            return  $parts[1] . ' млн. ' . $parts[2] . ' тыс.'; // Выведет "1 млрд. 234 млн. 567 тыс."
        }
        if($parts[1] == 0){
           return $parts[2] . ' тыс.';
        }
        else{
            return $parts[0] . ' млрд. ' . $parts[1] . ' млн. ' . $parts[2] . ' тыс.'; // Выведет "1 млрд. 234 млн. 567 тыс."
        }
    }
}
