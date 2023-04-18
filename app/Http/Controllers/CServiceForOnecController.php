<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class CServiceForOnecController extends Controller
{
    private $oneC; // Объявляем свойство

    public function __construct()
    {
        $this->oneC = new Client([ // Инициализируем свойство в конструкторе класса
            "base_uri" => "http://10.200.100.12/erp/hs/erp_api/erp_api/",
        ]);
    }

    public function LogicForService(Request $request){
        switch($request->action){
            case "getSumReport":
                return $this->getSumReport($request->action,$request->telegramId);
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
            default:
                return collect([
                    "message" => "send correct 'action' name"
                ]);
            }
    }
    private function checkAD($request){
      return null;
    }
    private function getSumReport($action,$id){
        $response = $this->oneC->request("POST", "?command=$action&id_telegram=$id");
        if ($response->getStatusCode() == 200) {
           return collect([
                "resp" => $data = json_decode($response->getBody(), true), 
                "status" => 200
            ]);
        } else {
            return null;
        }
   }
   private function setGetMoneyWorker(Request $request){
    $response = $this->oneC->request("POST", "?command=$request->action&id_telegram=$request->telegramId", ['json' => [
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
        $userGuid = DB::table("users as u")
        ->select(DB::raw("CONVERT(NVARCHAR(MAX), cu.GUID, 1) as GUID"))
        ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
        ->leftJoin("CRM_DOGOVOR as cd", "cd.MENEDZHER_GUID", "cu.GUID")
        ->where("u.ID", $request)
        ->limit(1)
        ->value("cu.GUID");
      //  return $userGuid;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://10.200.100.12/erpkz/hs/erp_api/erp_api/?command=getVisits&is_crm=True',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>"
            {
                'managerGuid': '$userGuid'
            }",
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic Y3JtX3VzZXI6Yk80bW9neWY=',
            'Content-Type: application/json' 
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
}
