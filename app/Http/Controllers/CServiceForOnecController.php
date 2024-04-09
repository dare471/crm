<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Response;
use Adldap\Laravel\Facades\Adldap;
use Adldap\Auth\AdldapException;
use phpDocumentor\Reflection\Types\Null_;

class CServiceForOnecController extends Controller
{
    private $oneC; // Объявляем свойство

    public function __construct(){
        $this->oneC = new Client([ // Инициализируем свойство в конструкторе класса
            "base_uri" => "http://10.200.100.12/erp/hs/erp_api/erp_api/",
        ]);
    }
    public function LogicForService(Request $request){
        switch($request->action){
            case "getSumReport":
                return $this->getSumReport($request->action,$request->userId);
                break;
            case "setGetMoneyWorker":
                return $this->setGetMoneyWorker($request);
                break;
            case "authActive":
                return $this->checkAD($request);
                break;
            case "getVisit": 
                return $this->getVisit($request);
                break;
            case "auth":
                return $this->authUser($request);
                break;
            case "getMyVacation":
                return $this->getMyVacation($request);
                break;
            case "getUserCertWork":
                return $this->getUserCertWork($request);
                break;
            case "getMyAuto":
                return $this->getMyAuto($request);
                break;
            case "getAutoByNumber":
                return $this->getAutoByNumber($request);
                break;
            case "setNeedOil":
                return $this->setNeedOil($request);
                break;
            case "setOdometerAuto":
                return $this->setOdometerAuto($request);
                break;
            case "getTasks":
                return $this->getTasks($request);
                break;
            case "getTasksByType":
                return $this->getTaskByType($request);
                break;
            case "getTaskByGuid":
                return $this->getTaskByGuid($request);
                break;
            case "setResultTask":
                return $this->setResultTask($request);
                break;
            case "getFile": 
                return $this->getFile($request);
                break;
            default:
                return collect([
                    "message" => "send correct 'action' name"
                ]);
            }
    }
    private function getSumReport($action,$id){
        $telegramId=$this->getTelegramUser($id);
        $response = $this->oneC->request("POST", "?command=$action&id_telegram=$telegramId");
        if ($response->getStatusCode() == 200) {
           return collect([
                "resp" => $data = json_decode($response->getBody(), true), 
                "status" => 200,
                "telegramId" => (int)$telegramId
            ]);
        } else {
            return collect(["message" => "get correct property"]);
        }
    }
    private function setGetMoneyWorker(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId", ['json' => [
            'date' => $request->date,
            'sum' => $request->sum,
            'desc' => $request->description
        ]]);
        if ($response->getStatusCode() == 200) {
        return collect(["data" => $data = json_decode($response->getBody(), true), "status" => 200]);
        } else {
            return null;
        }
    }
    public function getVisit($request){
        $trueUser = $this->getTrueGuidUser($request->userId);
        $innertionGuiduser = $this->innertionGuidUser($trueUser->GUID);
        //return collect(["sss" => trim($innertionGuiduser->BinaryGUID, ' ')]);
        // $userGuid = DB::table("users as u")
        // ->select(DB::raw("CONVERT(NVARCHAR(MAX), cu.GUID, 1) as GUID"))
        // ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
        // ->leftJoin("CRM_DOGOVOR as cd", "cd.MENEDZHER_GUID", "cu.GUID")
        // ->where("u.ID", $request)
        // ->limit(1)
        // ->value("cu.GUID");
      //  return $userGuid;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://10.200.100.12/erpkz/hs/erp_api/erp_api/?command=getVisits&guid_user=".$trueUser->GUID."",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>"
            {
                'managerGuid': '".trim($innertionGuiduser->BinaryGUID, ' ')."'
            }",
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic Y3JtX3VzZXI6Yk80bW9neWY='
        ),
        ));
  
        $response = curl_exec($curl);
  
        curl_close($curl);

        $res = json_decode($response);
       
        $oneC = collect($res->list_visit)->filter(function($item){
            return collect($item->clients)->where("clientIin", '<>', '')->isNotEmpty();
         });

         return $oneC->map(function($ietm){
            $check = DB::table("CRM_VISIT_TO_DATE")
                ->where("USER_ID", $this->getUserGuid($ietm->managerGuid))
                ->where("CLIENT_ID", $this->getClientIdWithGuid($ietm->clients[0]->clientIin))
                ->where("DATE_TO_VISIT", date('Y-m-d H:i:s',$ietm->planeDateToVisitStart))
                ->value("ID");
                if(is_null($check)){
                    $query = DB::table("CRM_VISIT_TO_DATE")
                    ->insertGetId([
                        "USER_ID" => $this->getUserGuid($ietm->managerGuid),
                        "CLIENT_ID" => $this->getClientIdWithGuid($ietm->clients[0]->clientIin),
                        "DATE_TO_VISIT" => date('Y-m-d H:i:s',$ietm->planeDateToVisitStart),
                        "STATUS" => 1,
                        "SOURCE" => 0
                    ]);
                    $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
                            ->insert([
                            "VISIT_ID" => $query,
                            "CLIENT_ID" => $this->getClientIdWithGuid($ietm->clients[0]->clientIin),
                            "TYPE_VISIT_ID" => $this->getTypeVisit($ietm->visitName),
                            "TYPE_MEETING" => 44,
                            "MEETING_COORDINATE" => NULL,
                            "PLOT" => NULL,
                            "DURATION" => NULL,
                            "DISTANCE" => NULL,
                            "STATUSVISIT" => 0
                            ]);
                    return collect(["q1"=> $query, "q2"=>$query2]);
                }
                else{
                    return $check;
                }
         });
    }
    public function setVisitToOneC($request){
        $response = $this->oneC->request("POST", "?command=getVisits&is_crm=True", ['json' => [
            '{
                "list_visit": [
                        {
                            "fullDay": "false",
                            "address": "УЛИЦА СТЕПНАЯ, дом 6, кв.1",
                            "managerGuid": "9147f314-36a3-11e6-80de-000c29e67b2e",
                            "factDateToVisitFinish": 1680258716,
                            "factDateToVisitStart": 1680258716,
                            "clients": [
                                {
                                    "clientIin": "030340007220",
                                    "clientName": "ТОО \"Агро-фирма Желкуар 2003\"",
                                    "guid1C": "1923bf1b-2f43-11e9-811d-000c29ef79ca",
                                    "clientId": 0
                                }
                            ],
                            "resultText1С": "Встреча завершене заключением договора",
                            "resultTextCRM": "Результат введенный в CRM",
                            "resultDataJSON": "JSON результата CRM",
                            "description": "краткое описание / план встречи",
                            "planeDateToVisitFinish": 1680255956,
                            "visitName": "Переговоры заключения договора + и чего то еще",
                            "targetGUID": "50dd55ee-c6c1-11ed-bfe3-b8cb29f95123",
                            "statusVisit": 3,
                            "planeDateToVisitStart": 1680255931,
                            "dateCreate": 1680577200,
                            "authorGuid": "9147f314-36a3-11e6-80de-000c29e67b2e",
                            "guid": "efaedd1b-c9f1-11ed-bfe3-b8cb29f95123",
                            "idCRM": 122222
                        },
                    ]
                }'
        ]]);
            if ($response->getStatusCode() == 200) {
                return collect(["data" => $data = json_decode($response->getBody(), true), "status" => 200]);
            } else {
                return null;
            }    
    }
    public function getTypeVisit($name){
        $query = DB::table("CRM_SPR_TYPE_VISIT")
        ->where("synonymOneC", $name)
        ->value("ID");
        return $query;
    }
    public function getUserGuid($userGUID){
        $userGuid = DB::table("users as u")
        // ->select(DB::raw("CONVERT(NVARCHAR(MAX), cu.GUID, 1) as GUID"))
        ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
        ->whereRaw("cu.GUID = CONVERT(binary(16), '$userGUID', 1)")
        ->limit(1)
        ->value("u.ID");
        return $userGuid;
    }
    public function getClientIdWithGuid($clientiin){
        $clientInf = DB::table("CRM_CLIENT_ID_GUID as ccig")
        // ->select(DB::raw("CONVERT(NVARCHAR(MAX), ccig.GUID, 1) as GUID"))
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
        ->where("cci.IIN_BIN", $clientiin)
        ->limit(1)
        ->value("cci.ID");
        switch($clientInf){ 
            case NULL:
                return 1174;
                break;
            default: 
            return $clientInf;
        }
        //return $clientInf;
        return $clientInf;
    }
    private function getMyVacation(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId");
        $fileContent = $response->getBody()->getContents();
        $response = new Response($fileContent);
        $response->header('Content-Type', 'application/pdf'); 
        $response->header('Content-Disposition', 'attachment; filename=file.pdf'); 
        return $response;
    }
    private function getUserCertWork(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId");
        $fileContent = $response->getBody()->getContents();
        $response = new Response($fileContent);
        $response->header('Content-Type', 'application/pdf'); 
        $response->header('Content-Disposition', 'attachment; filename=file.pdf'); 
        return $response;
    }
    private function getMyAuto(Request $request){
        if($request->userId == 1174){
            $telegramId=$this->getTelegramUser(1512);
        }
        else{
            $telegramId=$this->getTelegramUser($request->userId);
        }
        // $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId");
        if ($response->getStatusCode() == 200) {
            $guid = json_decode($response->getBody(), true);
            $rr = $this->oneC->request("POST", "?command=getAutoByGuid&id_telegram=$telegramId",['json' => [
                "guid" => $guid['Auto'][0]['guid']
            ]]);
            $oilCard = $this->oneC->request("POST", "?command=getMyOilCard&id_telegram=$telegramId",['json' => [
                "guid" => $guid['Auto'][0]['guid']
            ]]);
            return collect([
                 "resp" => $data = json_decode($rr->getBody(), true), 
                 "oilresp" => $data = json_decode($oilCard->getBody(), true),
                 "status" => 200,
                 "telegramId" => (int)$telegramId
             ]);
         } else {
             return collect(["message" => "get correct property"]);
         }
    }
    private function setNeedOil(Request $request){
        if($request->userId == 1174){
            $telegramId=$this->getTelegramUser(1512);
        }
        else{
            $telegramId=$this->getTelegramUser($request->userId);
        }
        $response = $this->oneC->request("POST", "?command=getMyAuto&id_telegram=$telegramId");
        if ($response->getStatusCode() == 200) {
            $guid = json_decode($response->getBody(), true);
            $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId", ["json" => [
                    "guid_oilCard" => $guid['Auto'][0]['guid'],
                    "guid_typeOil" => "4293af14-3e55-11ed-af90-d4f5ef107925",
                    "count" => $request->count,
                    "desc" => $request->description,
                    "due" => $request->date
            ]]);
            if ($response->getStatusCode() == 200) {
                return collect([
                    "resp" => json_decode($response->getBody(), true),
                ]);
            }
            else{
                return collect(
                    [
                        "message" => "get correct property"
                    ]
                );
            }
        }else{
            return collect(["message" => "get correct property"]);
        }
        
    }
    private function setOdometerAuto(Request $request){
        $files = $request->file('files');
        $arrFiles = collect([]);
        foreach ($files as $file) {
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $arrFiles->push($filename);
        }
        if($request->userId == 1174){
            $telegramId=$this->getTelegramUser(1512);
        }
        else{
            $telegramId=$this->getTelegramUser($request->userId);
        }
        $response = $this->oneC->request("POST", "?command=getMyAuto&id_telegram=$telegramId");
        if ($response->getStatusCode() == 200) {
            $guid = json_decode($response->getBody(), true);
            $setOdometr = $this->oneC->request("POST", "?command=getMyAuto&id_telegram=$telegramId", ["json" =>  [
                    "guid" => $guid['Auto'][0]['guid'],
                    "odometr" => $request->odometr,
                    "files" => $arrFiles
                ]
            ]);
            if ($setOdometr->getStatusCode() == 200) {
                return collect([
                    "resp" => json_decode($setOdometr->getBody(), true),
                ]);
            }
            else{
                return collect(
                    [
                        "message" => "get correct property"
                    ]
                );
            }
           }
    }
    private function getTasks(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId");
        if ($response->getStatusCode() == 200) {
            $guid = json_decode($response->getBody(), true);
           return collect($guid);
        }else{
            return collect(["message" => "get correct property"]);
        }
    }
    private function getTaskByType(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId", ['json' => [
            "task_type" => $request->taskType
        ]]);
        if ($response->getStatusCode() == 200) {
            $guid = json_decode($response->getBody(), true);
           return collect($guid);
        }else{
            return collect(["message" => "get correct property"]);
        }
    }
    private function getTaskByGuid(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId", ['json' => [
            "prefics" => $request->prefics,
            "guid" =>  $request->guidTask,
            "nameObject" => $request->nameObject
        ]]);
        if ($response->getStatusCode() == 200) {
            $guid = json_decode($response->getBody(), true);
            return collect($guid);
        }else{
            return collect(["message" => "get correct property"]);
        }
    }
    private function getFile(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId", ["json" =>[
                "task" =>[
                    "prefics" => $request->prefics,
                    "nameObject" => $request->nameObject,
                    "guid" => $request->guid
                ],
                "id_telegram" => $telegramId
        ]]);
        $fileContent = $response->getBody()->getContents();
        $response = new Response($fileContent);
        $response->header('Content-Type', 'application/pdf'); 
        $response->header('Content-Disposition', 'attachment; filename=file.pdf'); 
        return $response;
    }
    private function setResultTask(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        if($request->answer == true || $request->index == true){
            $answer = "Да";
            $index = 1;
        }
        else{
            $index = 0;
            $answer = "Нет";
        }
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId", ['json' => [
                "prefics" => $request->prefics,
                "guid" => $request->guidTask,
                "nameObject" => $request->nameObject,
                "taskResult" => [
                    "Комментарий" => $request->commentary,
                    "ВариантИмя" => $answer,
                    "Индекс" => $index
                ]
        ]]);
        if ($response->getStatusCode() == 200) {
            $guid = json_decode($response->getBody(), true);
            return collect($guid);
        }else{
            return collect(["message" => "get correct property"]);
        }
    }
    private function getAutoByNumber(Request $request){
        $telegramId=$this->getTelegramUser($request->userId);
        $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$telegramId", ['json' => [
            "number" => $request->number
        ]]);
        if ($response->getStatusCode() == 200) {
            $guid = json_decode($response->getBody(), true);
            $rr = $this->oneC->request("POST", "?command=getAutoByGuid&id_telegram=$telegramId",['json' => [
                "guid" => $guid['Auto'][0]['guid']
            ]]);
               
            return collect([
                 "resp" => $data = json_decode($rr->getBody(), true), 
                 "status" => 200,
                 "telegramId" => (int)$telegramId
             ]);
         } else {
             return collect(["message" => "get correct property"]);
         }
    }
    
    /// для ID && GUID
    private function getGuidUser($id){
        $userGuid = DB::table("users as u")
         ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
        ->where("u.id", $id)
        ->value("u.GUID");
        return $userGuid;
    }
    private function getTrueGuidUser($id){
        $query = DB::table("users as u")
        ->select(DB::raw('CONVERT(nvarchar(max), GUID, 1)'))
        ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
        ->where("u.id", $id)
        ->value("cu.GUID");
        $userGuid = DB::select("declare @BinaryGUID binary(16);
        SET @BinaryGUID = $query 
        SELECT dbo.sp_getid(@BinaryGUID) AS GUID");
        return $userGuid[0];
    }
    private function innertionGuidUser($guid){
        $query = DB::select("declare @GUIDasStr char(36),@GUID1С char(36);
        SET @GUID1С = '$guid'
        SET @GUIDasStr ='0x'+SUBSTRING(@GUID1С,20,4)+SUBSTRING(@GUID1С,25,13)+SUBSTRING(@GUID1С,15,4)+SUBSTRING(@GUID1С,10,4)+SUBSTRING(@GUID1С,1,8)
        SELECT CONVERT(nvarchar(MAX),@GUIDasStr,1) as BinaryGUID");
        return $query[0];
    }
    private function getTelegramUser($userId){
        return DB::table("users as u")
        ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
        ->where("u.id", $userId)
        ->value("cu.TELEGRAM_ID as telegramId"); 
    }
}
