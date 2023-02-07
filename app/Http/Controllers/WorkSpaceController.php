<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\clientLRegion;
use App\Http\Resources\clientInf;
use App\Http\Resources\managerRelation;
use App\Http\Resources\contractRelation;
use App\Http\Resources\managerContract;
use App\Http\Resources\addicionalContract;
use App\Http\Resources\clientsRFavoriteList;
use App\Http\Resources\contractHead;
use App\Http\Resources\plannedMeeting;
use App\Http\Resources\specificationContracts;
use App\Http\Resources\getMainInfCli;
use App\Http\Resources\sprClientBusinessPoint;
use App\Http\Resources\getContracts;
use App\Http\Resources\getLastContract;
use App\Http\Resources\getSubcides;
use App\Http\Resources\getSuppMngr;
use Carbon\Traits\Timestamp;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkSpaceController extends Controller
{
   use Timestamp;
   public function Contracts(Request $request)
    {
        if($request->type == "managerContracts"){
            return  ContractController::AllContracts($request);
        }
        if($request->type == "detailContract"){
            return ContractController::DetailContracts($request);
        }
    }
   public function Worktable(Request $request){
      if($request->type == "allRecords"){
         $query = DB::table('CRM_SEMENA')
         ->select(DB::raw('CONVERT(NVARCHAR(max), nomenklatura_guid, 1) as GUID'), 
         DB::raw('CONVERT(NVARCHAR(max), direksiya, 1) as DIRECTION_GUID'),
         'Остаток as REMAINDER',
         'Компании as COMPANIES',
         'Культура as CULTURE',
         'Регион AS REGION',
         'Название продукта AS PRODUCT_NAME',
         'Технология AS TECHNOLOGIES',
         'Подтверждение AS CHECKED',
         'Продано AS SALES',
         'Отгружено AS SHIPPED',
         'Не отгружено AS NOT_SHIPPED',
         'Стоки 2021 AS STOCKED_2021',
         'Стоки 2022 AS STOCKED_2022',
         'Стоки 2023 AS STOCKED_2023',
         'Поступление_2021 AS ADMISSION_2021',
         'Поступление_2022 AS ADMISSION_2022',
         'Поступление_2023 as ADMISSION_2023')
         ->get();
         return response()->json([
            "succes" => true,
            "status" => 201,
            "data" => $query
         ]);
      }
      if($request->type == "updateRecord"){
         $query = DB::statement("EXEC [CRM_DWH].[dbo].UPDATE_SEMENA_PODTVERZHDENIE
         @nomenklatura_guid= $request->guid  
         ,@direksiya= $request->directionGuid
         ,@PODTVERZHDENIE= $request->confirmation");
         return response()->json(['status' => true, 'message' => 'success', 'data'=> $query], 200);
      }
      if($request->type == "elevator"){
         if($request->action == "allRecords"){
            $query = DB::table("CRM_ELEVATOR")
            ->select("ID", "NAME", "BIN", "LOCATION", "STATION", "CONTACTS", "STORAGE_VOLUME")
            ->get();
            return response()->json([
               "succes" => true,
               "status" => 201,
               "data" => $query
            ]);
         }
         if($request->action == "detail"){
            $query = DB::table("CRM_ELEVATOR")
            ->select("ID", "NAME", "BIN", "LOCATION", "STATION", "CONTACTS", "STORAGE_VOLUME")
            ->where("ID", $request->recordId)
            ->get();
            return response()->json([
               "succees" => true,
               "status" => 201,
               "data" => $query
            ]);
          }
         if($request->action == "deleteRecord"){
            $query = DB::table("CRM_ELEVATOR")
            ->where("ID", $request->recordId)
            ->delete();
            return response()->json([
               "succees" => true,
               "status" => 201,
               "message" => "Records delete !!!"
            ]);
         }
         if($request->action == "updateRecord"){
            $query = DB::table()
            ->where("ID", $request->recordId)
            ->update([
               "CONTACTS" => $request->contactsRecord,
               "STORAGE_VOLUME" => $request->volumeRecord
            ]);
            return response()->json([
               "succes" => true,
               "status" => 201,
               "message" => "Records update"
            ]);
         }
         if($request->action == "addRecord"){
            $query = DB::table("CRM_ELEVATOR")
            ->insert(['NAME' => $request->nameRecord, 
            'BIN' => $request->binRecord, 
            'LOCATION' => $request->locationRecord, 
            'STATION' => $request->stationRecord, 
            'CONTACTS' => $request->contactsRecord, 
            'STORAGE_VOLUME' => $request->volumeRecord
         ]);
         return response()->json([
            "succees" => true,
            "status" => 201,
            "message" => "Record add"
         ]);
         }
      }
   }
   public function UserPlace(Request $request){
      if($request->type == "clientLRegion"){
         $query = DB::table("CRM_CLIENT_INFO as cci")
         ->join("CRM_CLIENT_ID_GUID as ccig", "ccig.ID", "cci.CLIENT_ID")
         ->select("NAME", 
         "ADDRESS", 
         "CLIENT_ID as clientId", 
         "IIN_BIN", 
         "CATO", DB::raw("CASE WHEN GUID IS NOT NULL then 'Постоянный клиент' ELSE 'Новый клиент' end as clientCheck"),         
         )
         ->where("REGION", $request->regionId)
         ->groupBy("NAME", "ADDRESS","GUID", "CLIENT_ID", "IIN_BIN", "CATO")
         ->get();
         return clientLRegion::collection($query)->all();
      }
      if($request->type == "clientInf"){
         $query = DB::table("CRM_CLIENT_INFO as cci")
         ->leftJoin("CRM_CLIENT_PROPERTIES_4326 as ccp", "ccp.client_info_id", "cci.id")
         ->leftJoin("CRM_CLIENT_CONTACTS as ccc", "ccc.CLIENT_ID", "cci.CLIENT_ID")
         ->select("cci.ID", 
         "ADDRESS", 
         "cci.CLIENT_ID", 
         "IIN_BIN", 
         "CATO", 
         "cci.DEYATELNOST",
         "ccc.NAME as contactName",
         "ccc.POSITION", 
         "ccc.PHONE_NUMBER",
         "ccc.EMAIL")
         ->where("cci.CLIENT_ID", $request->clientId)
         ->groupBy(
            "cci.NAME",
            "ADDRESS",
            "cci.DEYATELNOST",
            "cci.CLIENT_ID",
            "cci.ID",
            "IIN_BIN",
            "CATO",
            "ccc.NAME",
            "ccc.POSITION",
            "ccc.PHONE_NUMBER",
            "ccc.EMAIL"
         )
         ->get();
         return clientInf::collection($query)->all();
      }
      if($request->type == "managerContracts"){
         $query = DB::table("CRM_CLIENT_ID_GUID as ccig")
         ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
         ->leftJoin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
         ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
         ->select(DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID"),
         "cu.ID",
         "cu.NAIMENOVANIE as managerName",
         "cu.DIREKTSYA",
         "cu.DOLZHNOST",
         "ccig.ID as clientId",
         "cd.KONTRAGENT",
         "cd.NAIMENOVANIE",
         "cci.IIN_BIN",
         "SEZON",
         "cd.USLOVIYA_OPLATY",
         "cd.SPOSOB_DOSTAVKI",
         "cd.ADRES_DOSTAVKI",
         "cd.SUMMA_KZ_TG")
         ->where("cu.ID", $request->userId)
         ->where("cd.OSNOVNOY_DOGOVOR", "")
         ->whereIn("SEZON", ['Сезон 2022', 'Сезон 2021'])
         ->get();
         return managerContract::collection($query)->all();
      }
      if($request->type == "detailContract"){
         $query = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA as cds")
         ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "cds.DOGOVOR_GUID")
         ->select(DB::raw("CONVERT(NVARCHAR(MAX), DOGOVOR_GUID, 1) AS CONTRACT_GUID"), 
         "cd.NAIMENOVANIE",
         "PERIOD",
         DB::raw("CONVERT(NVARCHAR(MAX), NOMENKLATURA_GUID, 1) AS PRODUCT_GUID"),
         "NOMENKLATURA",
         "VIDY_KULTUR",
         "KOLICHESTVO",
         "TSENA",
         "TSENA_SO_SKIDKOY",
         "TSENA_PO_PRAYS_LISTU",
         "TSENA_MIN",
         "SUMMA",
         "SUMMA_SO_SKIDKOY",
         DB::raw("CONVERT(NVARCHAR(MAX), SKLAD_OTGRUZKI_GUID, 1) AS WAREHOUSE_GUID"),
         "SKLAD_OTGRUZKI",
         "cds.SUMMA_KZ_TG")
         ->whereRaw("DOGOVOR_GUID = $request->contractGuid")
         ->get();

         $contractHead = DB::table("CRM_CLIENT_ID_GUID as ccig")
         ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
         ->leftJoin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
         ->select(DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID"),
         "cu.ID", 
         "cu.NAIMENOVANIE", 
         "cu.DIREKTSYA", 
         "cu.DOLZHNOST", 
         "ccig.ID", 
         "cd.KONTRAGENT",
         "cd.NAIMENOVANIE as contractName",
         "SEZON",
         "cd.USLOVIYA_OPLATY",
         "cd.SPOSOB_DOSTAVKI",
         "cd.ADRES_DOSTAVKI",
         "cd.SUMMA_KZ_TG",
         "cd.NOMER_DOP_SOGLASHENIYA",
         DB::raw("CONVERT(NVARCHAR(MAX), CD.OSNOVNOY_DOGOVOR, 1) AS MAIN_CONTRACTS"))
         ->whereRaw("cd.GUID = $request->contractGuid")
         ->get();

         $maincontractguid = $contractHead[0]->CONTRACTS_GUID;

         $addicionalContract = DB::table("CRM_DOGOVOR as cd")
         ->select(DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS GUID"), 
         "cd.NAIMENOVANIE",
         DB::raw("CONVERT(NVARCHAR(MAX), OSNOVNOY_DOGOVOR, 1) as main"))
         ->where("cd.OSNOVNOY_DOGOVOR", $maincontractguid)
         ->get();
         return response()->json([
            "contractHead" => contractHead::collection($contractHead)->all(),
            "specificationContract" => specificationContracts::collection($query)->all(),
            "addicionalContract" => addicionalContract::collection($addicionalContract)->all()
         ]);
      }
      if($request->type == "managerRelation"){
         $query = DB::table("CRM_CLIENT_ID_GUID as cig")
         ->join("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "cig.GUID")
         ->select("cig.ID",
         "cd.NAIMENOVANIE",
         "cd.DATA_NACHALA_DEYSTVIYA",
         "cd.DATA_OKONCHANIYA_DEYSTVIYA",
         "cd.NOMER",
         "cd.STATUS",
         "cd.KONTRAGENT",
         "cd.MENEDZHER",
         "cd.SEZON",
         "cd.ADRES_DOSTAVKI",
         "cd.SUMMA_KZ_TG")
         ->where("cig.ID", $request->clientId)
         ->orderByDesc("DATA_NACHALA_DEYSTVIYA")
         ->get();
         
         $managerInf = DB::table("CRM_CLIENT_ID_GUID as ccig")
         ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
         ->leftJoin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
         ->select("cu.ID", 
         "cu.NAIMENOVANIE",
         "cu.DIREKTSYA",
         "cu.DOLZHNOST",
         "SEZON")
         ->where("ccig.ID", $request->clientId)
         ->groupBy("cu.ID", 
         "cu.NAIMENOVANIE",
         "cu.DIREKTSYA",
         "cu.DOLZHNOST",
         "SEZON")
         ->orderByDesc("SEZON")
         ->get();
         return response()->json([
            "contractData" => contractRelation::collection($query)->all(),
            "managerChrono" => managerRelation::collection($managerInf)->all()
         ]);
      }
      if($request->type == "allContractsClient"){
         $query = DB::table("CRM_CLIENT_ID_GUID as ccig")
         ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
         ->leftJOin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
         ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
         ->select(DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID"),
         "cu.ID",
         "cu.NAIMENOVANIE",
         "cu.DIREKTSYA",
         "cu.DOLZHNOST",
         "ccig.ID",
         "cd.KONTRAGENT",
         "cd.NAIMENOVANIE",
         "cci.IIN_BIN",
         "SEZON",
         "cd.USLOVIYA_OPLATY",
         "cd.SPOSOB_DOSTAVKI",
         "cd.ADRES_DOSTAVKI",
         "cd.SUMMA_KZ_TG")
         ->where("cci.CLIENT_ID", $request->clientId)
         ->where("cd.OSNOVNOY_DOGOVOR", "")
         ->whereIn("SEZON", ['Сезон 2023','Сезон 2022', 'Сезон 2021'])
         ->get();
         return response($query);
      }
      if($request->type == "addToFavorites"){
         $query = DB::table("CRM_CLIENT_TO_VISIT")
         ->insert([
            "USER_ID" => $request->userId, 
            "CLIENT_ID" => $request->clientId
         ]);
         return response()->json([
            "message" => "Client to favorites"
         ]);
      }
      if($request->type == "deleteToFavorites"){
         $query = DB::table("CRM_CLIENT_TO_VISIT")
         ->where("CLIENT_ID", $request->clientId)
         ->where("USER_ID", $request->userId)
         ->delete();
         return response()->json([
            "message" => "Client Delete to favorites"
         ]);
      }
      if($request->type == "clientsFavoriteList"){
         $query = DB::table("CRM_CLIENT_TO_VISIT as cctv")
         ->leftJoin("CRM_CLIENT_INFO as cci", "cci.ID", "cctv.CLIENT_ID")
         ->select("cctv.ID", "cctv.CLIENT_ID", "cci.NAME", "cci.IIN_BIN", "cci.ADDRESS")
         ->where("USER_ID", $request->userId)
         ->get();
         return clientsRFavoriteList::collection($query)->all();
      }
      if($request->type == "addDateToVisit"){
         $query = DB::table("CRM_VISIT_TO_DATE")
         ->insertGetId([
            "USER_ID" => $request->userId,
            "CLIENT_ID" => json_encode($request->properties),
            "DATE_TO_VISIT" => $request->dateToVisit
         ]);
         $arr = $request->properties;
         $i=0;
         foreach($arr as $a){
            $p=json_encode($a, true);
            $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
            ->insert([
               "VISIT_ID" => $query,
               "CLIENT_ID" => json_decode($p)->clientId,
               "TYPE_VISIT_ID" =>json_decode($p)->typeVisit,
               "TYPE_MEETING" => json_decode($p)->placeMeeting,
               "MEETING_COORDINATE" =>json_decode($p)->coordinate,
               "PLOT" => json_decode($p)->plotId,
               "DURATION" => json_decode($p)->duration,
               "DISTANCE" => json_decode($p)->distance
            ]);
         }
         return response()->json([
           "message" => "Meeting to save"
         ]);
      }
      if($request->type == "plannedMeeting"){
         $query = DB::table("CRM_VISIT_TO_DATE as cdtv")
         ->select("cdtv.DATE_TO_VISIT", "cdtv.ID", "CLIENT_ID", "css.NAME")
         ->leftJoin("CRM_SPR_STATUS as css", "css.ID", "cdtv.STATUS")
         ->where("USER_ID", $request->userId)
         ->get();
         $arr=[];
         $ar=[];
         $a=[];
         foreach($query as $q){
            $clientId = json_decode($q->CLIENT_ID);
            $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES as cdtvp")
            ->where("cdtvp.VISIT_ID", $q->ID)
            ->get();
            foreach($query2 as $c){
               $query=DB::table("CRM_CLIENT_INFO as cci")
               ->leftJoin("CRM_VISIT_TO_DATE_PROPERTIES as cdtvp", "cdtvp.CLIENT_ID", "cci.ID")
               ->leftJoin("CRM_SPR_TYPE_VISIT as cstv", "cstv.ID", "cdtvp.TYPE_VISIT_ID")
               ->leftJoin("CRM_SPR_TYPE_MEETING as cstm", "cdtvp.TYPE_MEETING", "cstm.ID")
               ->select("cci.ID", "cci.NAME", "IIN_BIN", "ADDRESS", "cdtvp.TYPE_VISIT_ID as visitId", "cdtvp.TIME_MEETING as timeMeeting", "cstv.NAME as visitName", "cstm.ID as meetingId", "cstm.NAME as meetingName", "cdtvp.PLOT as plotId")
               ->where("cdtvp.VISIT_ID", $q->ID)
               ->where("cci.ID", $c->CLIENT_ID)
               ->get();
            array_push($ar, $query[0]);
            }
            array_push($arr, 
            [
               "id" => (int)$q->ID, "dateToVisit" => $q->DATE_TO_VISIT, "statusVisit" => $q->NAME, "clients"=>plannedMeeting::collection($ar)->all()
            ]
         );
         $ar= array();
         }
        return $arr;
      }
      if($request->type == "choiceMeetingPlace"){
         if($request->id == 40){
            $query = DB::connection("mongodb")
            ->table("AllPlotsClient")
            ->where("clientId", $request->clientId)
            ->get();
            return $query;
         }
         if($request->id == 44){
            $query = DB::table("CRM_CLIENT_INFO")
            ->select("ADDRESS as clientAddress")
            ->where("ID", $request->clientId)
            ->get();

            $to = urlencode("г.Кокшетау, Северная промзона, У107");
            $from = urlencode($query[0]->clientAddress);

            $to_coord = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$to.'&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $to_coordinate = json_decode($to_coord, true);
            $t=$to_coordinate['results'][0]['geometry']['location'];

            $from_coord = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$from.'&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $from_coordinate = json_decode($from_coord, true);
            $f=$from_coordinate['results'][0]['geometry']['location'];

            $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?&origins='.$to.'&destinations='.$from.'&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $distance_arr = json_decode($distance_data);
            return response()->json([
               "officeCoordinate" => $t,
               "clientCoordinate" => $f,
               "directionMatrix"=> $distance_arr
          ]);
         }
         if($request->id == 48){
            $query = DB::table("CRM_CLIENT_BUSINESS_PLACE")
            ->insert([
               "CLIENT_ID" => $request->clientId,
               "NAME" => $request->placeName,
               "PLACE_ID" => $request->placeId,
               "COORDINATE" => json_encode($request->placeCoordinate)
            ]);
            return response()->json([
                'success' => true,
                'status' => 201,
            ]);
         }   
         if($request->handbook == "clientBusinessPoint"){
            $query = DB::table("CRM_SPR_BUSINESS_PLACE")
            ->get();
            return sprClientBusinessPoint::collection($query)->all();
         }
      }
      if($request->type == "changeVisit"){
         if($request->action == "setTime"){
            $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
            ->where("ID", $request->propId)
            ->update([
               "TIME_MEETING"=> $request->timeMeeting,
            ]);
            return response()->json([
               "$request->propId, set to time"
            ]);
         }
         if($request->action == "deleteClient"){
            $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
            ->where("ID", $request->propId)
            ->delete();
            return response()->json([
               "$request->propId, delete"
            ]);
         }  
      }
      if($request->type == "profileClient"){
         if($request->action == "getMainInf"){
            $query = DB::table("CRM_CLIENT_INFO as cci")
            ->where("cci.ID", $request->clientId)
            ->get();
            return getMainInfCli::collection($query)->all();
         }
         if($request->action == "getMngVisit"){
            $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES as cvtdp")
            ->leftJoin("CRM_VISIT_TO_DATE as cvtd", "cvtd.ID", "cvtdp.VISIT_ID")
            ->where("cvtdp.CLIENT_ID", $request->clientId)
            ->get();
            return response($query);
         }
         if($request->action == "getContracts"){
            $query = DB::table("CRM_DOGOVOR as cd")
            ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.GUID")
            ->where("ccig.ID", $request->clientId)
            ->paginate();
            if($query->isEmpty()){
               return response()->json([
                 "message" =>  "didn't contracts",
                 "status" => false
               ]);
            }
            else{
               return getContracts::collection($query);
            }
         }
         if($request->action == "getSubcides"){
            $query = DB::table("CRM_SHYMBULAK_SUBSIDIES as css")
            ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "css.APPLICANT_IIN_BIN")
            ->where("cci.ID", $request->clientId)
            ->get();
            return getSubcides::collection($query)->all();
         }
         if($request->action == "setContacts"){
            $query = DB::table("CRM_CLIENT_CONTACTS")
            ->where("CLIENT_ID", $request->clientId)
            ->where("ID", $request->contactId);
            if($request->position){
               $query->update([
                  "POSITION" => $request->position,
               ]);
            }
            if($request->name){
               $query->update([
                  "NAME" => $request->name
               ]);
            }
            if($request->phoneNumber){
               $query->update([
                  "PHONE_NUMBER" => $request->phoneNumber
               ]);
            }
            if($request->email){
               $query->update([
                  "EMAIL" => $request->email
               ]);
            }
            return response()->json([
               "message" => "Records update",
               "status" => true
            ]);;
         }
         if($request->action == "setMainInf"){
            $query = DB::table("CRM_CLIENT_INFO")
            ->where("ID", $request->clientId)
            ->update([
               "ADDRESS" => $request->address
            ]);
            return response()->json([
               "message" => "Record update",
               "status" => true
            ]);
         }
         if($request->action == "getSuppMngr"){
            $subQuery = DB::table("CRM_CLIENT_ID_GUID as ccig")
            ->select("cu.ID", "cu.NAIMENOVANIE", "cu.DIREKTSYA", "cu.DOLZHNOST", "SEZON")
            ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
            ->leftJoin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
            ->where("ccig.ID", $request->clientId)
            ->groupBy("cu.ID", "cu.NAIMENOVANIE", "cu.DIREKTSYA", "cu.DOLZHNOST", "SEZON")
            ->orderByDesc("SEZON")
            ->get();

          return getSuppMngr::collection($subQuery)->all();
         }
         if($request->action == "getLastContract"){
            $query = DB::table("CRM_CLIENT_ID_GUID as cig")
            ->select("cd.ID","cd.NAIMENOVANIE", "cd.DATA_NACHALA_DEYSTVIYA","cd.DATA_OKONCHANIYA_DEYSTVIYA", "cd.NOMER", "cd.STATUS", "cd.KONTRAGENT", "cd.MENEDZHER AS manager", "cd.SEZON", "cd.ADRES_DOSTAVKI", "cd.SUMMA_KZ_TG")
            ->join("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "cig.GUID")
            ->where("cig.ID", $request->clientId)
            ->get();
            return getLastContract::collection($query)->all(); 
         }
      }
   }
   // public function __construct()
   // {
   //     $this->middleware('auth:api');
   // }

   // protected function guard() {
   //     return Auth::guard();
   // }
}