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
            "clientId" => (int)$this->ID,
            "clientName" => $this->NAME,
            "clientCategory" => trim($this->buisnessCategory, ' '),
            "address" => $this->ADDRESS,
            "contactInf" => $this->contactInf($this->CLIENT_ID),
            "clientIin" => (int)$this->IIN_BIN,
            "managerId" => (int)$this->managerId,
            "managerName" => $this->managerLinkClient($this->managerId),
            "startVisit" => $this->STARTVISIT,
            "finishVisit" => $this->FINISHVISIT,
            "statusVisit" => (Boolean)$this->STATUSVISIT,
            "visitTypeId" => (int)$this->TYPE_VISIT_ID,
            "vistiTypeName" => $this->sprVisit($this->TYPE_VISIT_ID),
            "meetingTypeId" => (int)$this->TYPE_MEETING,
            "meetingTypeName" => $this->sprMeeting((int)$this->TYPE_MEETING),
            "meetingCoordinate" => $this->MEETING_COORDINATE,
            "plotId" => (int)$this->PLOT,
            "plotName" => $this->plotSpr($this->PLOT),
            "summContract" => $this->sumContractsAll($this->CLIENT_ID). " ₸",
            "summCurrentContractSeason" => $this->sumCurrentSeasonContracts($this->CLIENT_ID). " ₸",
            "checkContracts" => (boolean)$this->contractList($this->CLIENT_ID),
            "potentialClientPercent" => $this->potentialClient($this->ID, $this->sumCurrentSeasonContracts($this->CLIENT_ID))."%",
            "potentialClient" => $this->potentialClient($this->ID, null),
            "subscidesSum" => $this->subcidesSum($this->IIN_BIN)." ₸",
            "checkSubscides" => (boolean)$this->subcidesAll($this->IIN_BIN),
            "duration" => $this->DURATION,
            "distance" => $this->DISTANCE
        ];
    }
    private function onecVisit($iin){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://10.200.100.11/erp_alex/hs/erp_api/erp_api/?command=getVisits&is_crm=True',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "dataStart":1678803625,
            "dataEnd":1680704425,
            "managerGuid ": "6d9f3f35-a650-11eb-af7b-d4f5ef107925"
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic dGVsZWdyYW1ib3Q6dk8za3lneW0=',
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }
    private function contactInf($id){
        $contact = DB::table("CRM_CLIENT_CONTACTS")
        ->where("CLIENT_ID", 1)
        ->get();
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
            return $p;
        }
        else{
            $p = DB::table("CRM_CLIENT_INFO")
            ->where("ID", $id)
            ->value("potential");
                if($p == null){
                    $pt = '100';
                }
                else{
                    $pt = $p / $sumcontract * 100;
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
            ->where("ccig.ID", $id)
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
            ->where("ccig.ID", $id)
            ->where("cd.SEZON", "Сезон 2023")
            ->sum("SUMMA_KZ_TG");
            return $this->classificationSum((int)$sum);
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
