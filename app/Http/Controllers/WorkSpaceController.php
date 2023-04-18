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
use App\Http\Resources\getBusinessPoint;
use App\Http\Resources\GetClientXL;
use App\Http\Resources\plannedMeeting;
use App\Http\Resources\getHandbook;
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
use App\Http\Resources\PlannedMettingDetail;
class WorkSpaceController extends Controller
{
   use Timestamp;
   public function Contracts(Request $request)
   {
      if ($request->type == "managerContracts") {
         return  ContractController::AllContracts($request);
      }
      if ($request->type == "detailContract") {
         return ContractController::DetailContracts($request);
      }
   }
   public function Worktable(Request $request)
   {
      if ($request->type == "allRecordsBakhyt"){
         $query = DB::table('CRM_SEMENA_2023')
            ->select(
               DB::raw('DISTINCT CONVERT(NVARCHAR(max), NOMENKLATURA_GUID, 1) as GUID'),
               "NOMENKLATURA",
               "PROIZVODITELI",
               "VIDY_KULTUR_NOMENKLATURY",
               "DIREKSYA",
               DB::raw('CONVERT(NVARCHAR(max), DIREKSYA_GUID, 1) as DIREKSYA_GUID'),
               DB::raw("SUM(PLAN_2023) OVER(PARTITION BY [NOMENKLATURA_GUID],[DIREKSYA_GUID]) as plan2023 "),
               DB::raw("SUM(PRODANO) OVER(PARTITION BY [NOMENKLATURA_GUID],[DIREKSYA_GUID]) as salesSum"),
               DB::raw("SUM([OTGRUZHENO]) OVER(PARTITION BY [NOMENKLATURA_GUID],[DIREKSYA_GUID]) AS otrgr"),
               DB::raw("PRODANO_SUM AS sales"),
               DB::raw("PLAN_SUM AS planSum"),
               DB::raw("MAX([OSTATOK_Bakhyt])OVER(PARTITION BY [NOMENKLATURA_GUID],[DIREKSYA_GUID]) as ostBakhyt "),
               DB::raw("MAX(PODTVERZHDENO_Bakhyt) OVER(PARTITION BY [NOMENKLATURA_GUID],[DIREKSYA_GUID]) AS potBakhyt")
            )
            ->whereNotNull("DIREKSYA_GUID")
            ->orderByDesc("ostBakhyt")
            ->get();
         return response()->json([
            "succes" => true,
            "status" => 201,
            "data" => $query
         ]);
      }
      if ($request->type == "updateRecord") {
         $query = DB::statement("EXEC [CRM_DWH].[dbo].UPDATE_SEMENA_PODTVERZHDENIE_Bakhyt
            @NOMENKLATURA= $request->guid  
            ,@DIREKSYA= $request->directionGuid
            ,@PODTVERZHDENIE= $request->confirmation");
         return response()->json(['status' => true, 'message' => 'success', 'data' => $query], 200);
      }
      if ($request->type == "elevator") {
         if ($request->action == "allRecords") {
            $query = DB::table("CRM_ELEVATOR")
               ->select("ID", "NAME", "BIN", "LOCATION", "STATION", "CONTACTS", "STORAGE_VOLUME")
               ->get();
            return response()->json([
               "succes" => true,
               "status" => 201,
               "data" => $query
            ]);
         }
         if ($request->action == "detail") {
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
         if ($request->action == "deleteRecord") {
            $query = DB::table("CRM_ELEVATOR")
               ->where("ID", $request->recordId)
               ->delete();
            return response()->json([
               "succees" => true,
               "status" => 201,
               "message" => "Records delete !!!"
            ]);
         }
         // if($request->action == "updateRecord"){
         //    $query = DB::table()
         //    ->where("ID", $request->recordId)
         //    ->update([
         //       "CONTACTS" => $request->contactsRecord,
         //       "STORAGE_VOLUME" => $request->volumeRecord
         //    ]);
         //    return response()->json([
         //       "succes" => true,
         //       "status" => 201,
         //       "message" => "Records update"
         //    ]);
         // }
         if ($request->action == "addRecord") {
            $query = DB::table("CRM_ELEVATOR")
               ->insert([
                  'NAME' => $request->nameRecord,
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
      switch ($request->type) {
         case "clientLRegion":
            return $this->clientLRegion($request);
         case "clientInf":
            return $this->clientInf($request);
         case "managerContracts":
            return $this->managerContracts($request);
         case "detailContract":
            return $this->detailContract($request);
         case "managerRelation":
            return $this->managerRelation($request); 
         case "allContractClient":
            return $this->allContractClient($request);
         case "addToFavorites":
            return $this->addToFavorites($request);
         case "deleteToFavorites":
            return $this->deleteToFavorites($request);
         case "clientsFavoriteList":
            return $this->clientsFavoriteList($request);
         case "addDateToVisit":
            return $this->addDateToVisit($request);
         case "plannedMeeting":
            if($request->action == "getMeetings"){
               return $this->plannedMeeting($request);
            }
            if($request->action == "getDetailMeeting"){
               return $this->plannedMeetingDetailMob($request);
            }
            else{
               return $this->plannedMeeting($request);
            }
         case "plannedMeetingMob":
            if($request->action == "getMeetings"){
               return $this->plannedMeeting($request);
            }
            if($request->action == "setStartVisit"){
               return $this->plannedMeetingMob($request);
            }
            if($request->action == "setFinishVisit"){
               return $this->plannedMeetingMob($request);
            }
            if($request->action == "getDetailMeeting"){
               return $this->plannedMeetingDetailMob($request);
            }
         case "plannedMeetingDetail":
            return $this->plannedMeetingDetail($request);
         case "plannedMeetingDetailMob":
            return $this->plannedMeetingDetailMob($request);
         case "meetingSurvey":
            return $this->meetingSurvey($request);
         case "choiceMeetingPlace":
            return $this->choiceMeetingPlace($request);
         case "changeVisit":
            return $this->changeVisit($request);
         case "profileClient":
            return $this->profileClient($request);
         case "uploadFile":
            return $this->UploadFiles($request);
         case "onecVisit":
            return $this->onecVisit($request);
         default:
            return null; // or throw an exception, depending on your needs
      }
   }
   private function clientLRegion(Request $request){
      $query = DB::table("CRM_CLIENT_INFO as cci")
         ->join("CRM_CLIENT_ID_GUID as ccig", "ccig.ID", "cci.CLIENT_ID")
         ->select(
            "NAME",
            "ADDRESS",
            "CLIENT_ID as clientId",
            "IIN_BIN",
            "CATO",
            DB::raw("CASE WHEN GUID IS NOT NULL then 'Постоянный клиент' ELSE 'Новый клиент' end as clientCheck"),
         )
         ->where("REGION", $request->regionId)
         ->groupBy("NAME", "ADDRESS", "GUID", "CLIENT_ID", "IIN_BIN", "CATO")
         ->get();
      return clientLRegion::collection($query)->all();
   }
   private function clientInf(Request $request){
      $query = DB::table("CRM_CLIENT_INFO as cci")
         ->leftJoin("CRM_CLIENT_PROPERTIES_4326 as ccp", "ccp.client_info_id", "cci.id")
         ->leftJoin("CRM_CLIENT_CONTACTS as ccc", "ccc.CLIENT_ID", "cci.CLIENT_ID")
         ->select(
            "cci.ID",
            "ADDRESS",
            "cci.CLIENT_ID",
            "IIN_BIN",
            "CATO",
            "cci.DEYATELNOST",
            "ccc.NAME as contactName",
            "ccc.POSITION",
            "ccc.PHONE_NUMBER",
            "ccc.EMAIL"
         )
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
   private function managerContracts(Request $request){
      $query = DB::table("CRM_CLIENT_ID_GUID as ccig")
            ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
            ->leftJoin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
            ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
            ->select(
               DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID"),
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
               "cd.SUMMA_KZ_TG"
            )
            ->where("cu.ID", $request->userId)
            ->where("cd.OSNOVNOY_DOGOVOR", "")
            ->whereIn("SEZON", ['Сезон 2022', 'Сезон 2021'])
            ->get();
         return managerContract::collection($query)->all();
   }
   private function detailContract(Request $request){
         $query = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA as cds")
         ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "cds.DOGOVOR_GUID")
         ->select(
            DB::raw("CONVERT(NVARCHAR(MAX), DOGOVOR_GUID, 1) AS CONTRACT_GUID"),
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
            "cds.SUMMA_KZ_TG"
         )
         ->whereRaw("DOGOVOR_GUID = $request->contractGuid")
         ->get();

      $contractHead = DB::table("CRM_CLIENT_ID_GUID as ccig")
         ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
         ->leftJoin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
         ->select(
            DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID"),
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
            DB::raw("CONVERT(NVARCHAR(MAX), CD.OSNOVNOY_DOGOVOR, 1) AS MAIN_CONTRACTS")
         )
         ->whereRaw("cd.GUID = $request->contractGuid")
         ->get();

      $maincontractguid = $contractHead[0]->CONTRACTS_GUID;

      $addicionalContract = DB::table("CRM_DOGOVOR as cd")
         ->select(
            DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS GUID"),
            "cd.NAIMENOVANIE",
            DB::raw("CONVERT(NVARCHAR(MAX), OSNOVNOY_DOGOVOR, 1) as main")
         )
         ->where("cd.OSNOVNOY_DOGOVOR", $maincontractguid)
         ->get();
      return response()->json([
         "contractHead" => contractHead::collection($contractHead)->all(),
         "specificationContract" => specificationContracts::collection($query)->all(),
         "addicionalContract" => addicionalContract::collection($addicionalContract)->all()
      ]);
   }
   private function managerRelation(Request $request){
      $query = DB::table("CRM_CLIENT_ID_GUID as cig")
            ->join("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "cig.GUID")
            ->select(
               "cig.ID",
               "cd.NAIMENOVANIE",
               "cd.DATA_NACHALA_DEYSTVIYA",
               "cd.DATA_OKONCHANIYA_DEYSTVIYA",
               "cd.NOMER",
               "cd.STATUS",
               "cd.KONTRAGENT",
               "cd.MENEDZHER",
               "cd.SEZON",
               "cd.ADRES_DOSTAVKI",
               "cd.SUMMA_KZ_TG"
            )
            ->where("cig.ID", $request->clientId)
            ->orderByDesc("DATA_NACHALA_DEYSTVIYA")
            ->get();

         $managerInf = DB::table("CRM_CLIENT_ID_GUID as ccig")
            ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
            ->leftJoin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
            ->select(
               "cu.ID",
               "cu.NAIMENOVANIE",
               "cu.DIREKTSYA",
               "cu.DOLZHNOST",
               "SEZON"
            )
            ->where("ccig.ID", $request->clientId)
            ->groupBy(
               "cu.ID",
               "cu.NAIMENOVANIE",
               "cu.DIREKTSYA",
               "cu.DOLZHNOST",
               "SEZON"
            )
            ->orderByDesc("SEZON")
            ->get();
         return response()->json([
            "contractData" => contractRelation::collection($query)->all(),
            "managerChrono" => managerRelation::collection($managerInf)->all()
         ]);
   }
   private function allContractClient(Request $request){
      $query = DB::table("CRM_CLIENT_ID_GUID as ccig")
            ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
            ->leftJOin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
            ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
            ->select(
               DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID"),
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
               "cd.SUMMA_KZ_TG"
            )
            ->where("cci.CLIENT_ID", $request->clientId)
            ->where("cd.OSNOVNOY_DOGOVOR", "")
            ->whereIn("SEZON", ['Сезон 2023', 'Сезон 2022', 'Сезон 2021'])
            ->get();
         return response($query);
   }
   private function addToFavorites(Request $request){
      foreach ($request->clientId as $client_Id) {
         $query = DB::table("CRM_CLIENT_TO_VISIT")
            ->insert([
               "USER_ID" => $request->userId,
               "CLIENT_ID" => $client_Id
            ]);
      }
      return response()->json([
         "message" => "Client to favorites"
      ]);
   }
   private function deleteToFavorites(Request $request){
      $query = DB::table("CRM_CLIENT_TO_VISIT")
      ->where("CLIENT_ID", $request->clientId)
      ->where("USER_ID", $request->userId)
      ->delete();
      return response()->json([
         "message" => "Client Delete to favorites"
      ]);
   }
   private function clientsFavoriteList(Request $request){
      $query = DB::table("CRM_CLIENT_TO_VISIT as cctv")
         ->leftJoin("CRM_CLIENT_INFO as cci", "cci.ID", "cctv.CLIENT_ID")
         ->select("cctv.ID", "cctv.CLIENT_ID", "cci.NAME", "cci.IIN_BIN", "cci.ADDRESS")
         ->where("USER_ID", $request->userId)
         ->get();
      return clientsRFavoriteList::collection($query)->all();
   }
   private function addDateToVisit (Request $request){
      $query = DB::table("CRM_VISIT_TO_DATE")
      ->insertGetId([
         "USER_ID" => $request->userId,
         "CLIENT_ID" => json_encode($request->properties),
         "DATE_TO_VISIT" => $request->dateToVisit,
         "SOURCE" => 1
      ]);
      $arr = $request->properties;
      $i = 0;
      foreach ($arr as $a) {
         $p = json_encode($a, true);
         $coord=json_decode($p)->coordinate;
         $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
            ->insert([
               "VISIT_ID" => $query,
               "CLIENT_ID" => json_decode($p)->clientId,
               "TYPE_VISIT_ID" => json_decode($p)->typeVisit,
               "TYPE_MEETING" => json_decode($p)->placeMeeting,
               "MEETING_COORDINATE" => json_encode($coord),
               "PLOT" => json_decode($p)->plotId,
               "DURATION" => json_decode($p)->duration,
               "DISTANCE" => json_decode($p)->distance,
               "STATUSVISIT" => 0
            ]);
      }
      return response()->json([
         "message" => "Meeting to save"
      ]);
   }
   
   private function plannedMeeting(Request $request){
     app('App\Http\Controllers\CServiceForOnecController')->getVisit($request->userId);
      $query = DB::table("CRM_VISIT_TO_DATE as cdtv")
         ->select("cdtv.DATE_TO_VISIT", "cdtv.ID", "CLIENT_ID", "css.NAME", "cdtv.SOURCE")
         ->leftJoin("CRM_SPR_STATUS as css", "css.ID", "cdtv.STATUS")
         ->where("USER_ID", $request->userId)
         ->get();
         $arr = [];
         $ar = [];
         foreach ($query as $q) {
            $queryDate = $q->DATE_TO_VISIT;
            $clientId = json_decode($q->CLIENT_ID);
            $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES as cdtvp")
               ->where("cdtvp.VISIT_ID", $q->ID)
               ->get();
            foreach ($query2 as $c) {
               $query = DB::table("CRM_CLIENT_INFO as cci")
                  ->leftJoin("CRM_VISIT_TO_DATE_PROPERTIES as cdtvp", "cdtvp.CLIENT_ID", "cci.ID")
                  ->leftJoin("CRM_SPR_TYPE_VISIT as cstv", "cstv.ID", "cdtvp.TYPE_VISIT_ID")
                  ->leftJoin("CRM_SPR_TYPE_MEETING as cstm", "cdtvp.TYPE_MEETING", "cstm.ID")
                  ->select("cdtvp.id as visitId", "cci.ID", "cdtvp.STATUSVISIT as statusVisit",  "cci.NAME", DB::raw("'$queryDate' as dateVisit"), "IIN_BIN", "ADDRESS", "cdtvp.TYPE_VISIT_ID as visitTypeId", "cdtvp.TIME_MEETING as timeMeeting", "cstv.NAME as visitTypeName", "cstm.ID as meetingTypeId", "cstm.NAME as meetingTypeName", "cdtvp.PLOT as plotId")
                  ->where("cdtvp.VISIT_ID", $q->ID)
                  ->where("cci.ID", $c->CLIENT_ID)
                  ->limit(1)
                  ->get();
                  $ar=collect($query);
               
            }
            if($request->view == "listFormat"){
               $arr = plannedMeeting::collection($ar)->all();
            }
            else{
               array_push(
                  $arr,
                  collect([
                     "id" => (int)$q->ID, "dateToVisit" => $q->DATE_TO_VISIT, "statusVisit" => $q->NAME, "clients" => plannedMeeting::collection($ar)->all()
                  ])
            );
            $ar = array();
            }
         }
         return collect($arr)->filter(function($item){
            return !empty($item['clients']);
         });
         //return $this->plannedMeeting($request);
   }
   private function plannedMeetingMob(Request $request){
      
      if($request->action == "setStartVisit"){
         $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
         ->where("id", $request->visitId)
         ->update([
            "STARTVISIT" => DB::raw("GETDATE()")
         ]);
         return response()->json([
            "message" => "startdate Fixed"
         ]);
      }
      if($request->action == "setFinishVisit"){
         $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
         ->where("id", $request->visitId)
         ->update([
            "FINISHVISIT" => DB::raw("GETDATE()")
         ]);
         return response()->json([
            "message" => "finishdate Fixed"
         ]);
      }
      else{
         $query = DB::table("CRM_VISIT_TO_DATE as cdtv")
         ->select("cdtv.DATE_TO_VISIT", "cdtv.ID", "CLIENT_ID", "css.NAME")
         ->leftJoin("CRM_SPR_STATUS as css", "css.ID", "cdtv.STATUS")
         ->where("USER_ID", $request->userId)
         ->get();
         $arr = [];
         $ar = [];
         $a = [];
         foreach ($query as $q) {
            $queryDate = $q->DATE_TO_VISIT;
            $clientId = json_decode($q->CLIENT_ID);
            $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES as cdtvp")
               ->where("cdtvp.VISIT_ID", $q->ID)
               ->get();
            foreach ($query2 as $c) {
               $query = DB::table("CRM_CLIENT_INFO as cci")
                  ->leftJoin("CRM_VISIT_TO_DATE_PROPERTIES as cdtvp", "cdtvp.CLIENT_ID", "cci.ID")
                  ->leftJoin("CRM_SPR_TYPE_VISIT as cstv", "cstv.ID", "cdtvp.TYPE_VISIT_ID")
                  ->leftJoin("CRM_SPR_TYPE_MEETING as cstm", "cdtvp.TYPE_MEETING", "cstm.ID")
                  ->select("cdtvp.id as visitId", "cci.ID", "cdtvp.STATUSVISIT as statusVisit", "cci.NAME", DB::raw("'$queryDate' as dateVisit"), "IIN_BIN", "ADDRESS", "cdtvp.TYPE_VISIT_ID as visitTypeId", "cdtvp.TIME_MEETING as timeMeeting", "cstv.NAME as visitTypeName", "cstm.ID as meetingTypeId", "cstm.NAME as meetingTypeName", "cdtvp.PLOT as plotId")
                  ->where("cdtvp.VISIT_ID", $q->ID)
                  ->where("cci.ID", $c->CLIENT_ID)
                  ->get();
               array_push($ar, $query[0]);
            }
            if($request->view == "listFormat"){
                  $arr = plannedMeeting::collection($ar)->all();
            }
            else{
               array_push(
                  $arr,
                  [
                     "id" => (int)$q->ID, "dateToVisit" => $q->DATE_TO_VISIT, "statusVisit" => $q->NAME, "clients" => plannedMeeting::collection($ar)->all()
                  ]
            );
            $ar = array();
         }
      }
      return $arr;
      }
    
   }
   private function plannedMeetingDetail(Request $request){
      $query = DB::table("CRM_SURVEY_MEETING")
      ->where("visitId", $request->visitId)
      ->get();
      // return $query;
      $a=[];
      $ar=[];
      $arr=[];
      foreach($query as $q){
         $workdone = DB::table("CRM_SPR_WORK_DONE")
         ->whereIn("id", json_decode($q->workdoneId))
         ->get();
         $recarr = json_decode($q->recomendationId);
         $fieldins = json_decode($q->fieldInspectionId);
         $contractc = json_decode($q->contractComplicationsId);
         foreach($recarr as $r){
            $recomend = DB::table("CRM_SPR_RECOMENDATIONS_MEETING")
            ->select("id","name", DB::raw("'$r->description' as description"))
            ->where("id", $r->recomendationTypeId)
            ->get();
           array_push($ar, $recomend);
         }
         foreach($fieldins as $f){
            $fieldsinsp = DB::table("CRM_SPR_FIELD_INSPECTION")
            ->select("id", "name", "category", DB::raw("'$f->description' as description"))
            ->where("id", $f->inspectionTypeId)
            ->get();
            array_push($arr, $fieldsinsp);
         }
         $contract = DB::table("CRM_SPR_CONTRACT_COMPLICATIONS")
         ->whereIn("id", $contractc)
         ->get();

         $a=["contractComplication"=> $contract, "workDone" => $workdone, "recomend" => $ar[0], "fieldsInsp"=>$arr[0]];
      }
      return $a;
   }
   private function plannedMeetingDetailMob(Request $request){
      
      $queryClient = DB::table("CRM_VISIT_TO_DATE_PROPERTIES as cvdp")
      ->leftJoin("CRM_CLIENT_INFO as cci", "cci.ID", "cvdp.CLIENT_ID")
      ->where("cvdp.ID", $request->visitId)
      ->get();
      return PlannedMettingDetail::collection($queryClient)
         ->map(function ($item) {
            return $item;
         })->first();
   }
   private function meetingSurvey(Request $request){
      if($request->action == "fixedSurvey"){
         if ($request->hasFile('file')) {
            // Получаем файл
            $file = $request->file('file');
            // Генерируем уникальное имя для файла
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            // Сохраняем файл на диск
            $query = DB::table("CRM_SURVEY_MEETING")
            ->insert([
               "visitId" => $request->visitId,
               "fieldInspectionId" => json_encode($request->fieldInspection),
               "recomendationId" => json_encode($request->recomendation),
               "contractComplicationsId" => json_encode($request->contractComplication),
               "workdoneId" => json_encode($request->workDone),
               "fileVisit" => '/uploads/' . $filename
            ]);
            $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
            ->where("ID", $request->visitId)
            ->update([
               "STATUSVISIT" => "1"
            ]);
            // Возвращаем ответ с информацией о сохраненном файле
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'data' => [
                    'file_name' => $filename,
                    'file_path' => '/uploads/' . $filename,
                ],
            ]);
         }
         if($request->action == "fileForRecomendation"){
            return $this->recomendationSurvey($request);
         }
         else{
            //return $request;
            $query = DB::table("CRM_SURVEY_MEETING")
            ->insert([
               "visitId" => $request->visitId,
               "fieldInspectionId" => json_encode($request->fieldInspection),
               "recomendationId" => json_encode($request->recomendation),
               "contractComplicationsId" => json_encode($request->contractComplication),
               "workdoneId" => json_encode($request->workDone),
               "fileVisit" => json_encode($request->fileVisit)
            ]);
            $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
            ->where("ID", $request->visitId)
            ->update([
               "STATUSVISIT" => "1"
            ]);
            return response()->json([
               'success' => true,
               "recomendationId" => json_encode($request->recomendation),
               "recomendation" => $request->recomendation,
               
           ]);
         }
      }
      if($request->action == "getHandBookWorkDone"){
         $query = DB::table("CRM_SPR_WORK_DONE")
         ->get();
         return getHandbook::collection($query)->all();
      }
      if($request->action == "getHandBookFieldInsp"){
         $query = DB::table("CRM_SPR_FIELD_INSPECTION as csfi")
         ->select("csfi.id", "csfi.name", "csfi.url as url","csffi.name as categoryName", "csffi.id as categoryId")
         ->leftJoin("CRM_SPR_FOR_FIELD_INSPECTION as csffi", "csffi.id", "csfi.category")
         ->get();
         return getHandbook::collection($query)->all();
      }
      if($request->action == "getHandBookContractComplications"){
         $query = DB::table("CRM_SPR_CONTRACT_COMPLICATIONS")
         ->get();
         return getHandbook::collection($query)->all();
      }
      if($request->action == "getHandBookMeetingRecommendations"){
         $query = DB::table("CRM_SPR_RECOMENDATIONS_MEETING")
         ->get();
         return getHandbook::collection($query)->all();
      }
   }
   private function choiceMeetingPlace($request){
      switch($request->id){
         case 40:
            $query = DB::connection("mongodb")
            ->table("AllPlotsClient")
            ->where("clientId", $request->clientId)
            ->get();
         return $query;
         break;
         case 44:
            $query = DB::table("CRM_CLIENT_INFO")
            ->select("ADDRESS as clientAddress")
            ->where("ID", $request->clientId)
            ->get();

            $to = urlencode("г.Кокшетау, Северная промзона, У107");
            $from = urlencode($query[0]->clientAddress);

            $to_coord = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $to . '&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $to_coordinate = json_decode($to_coord, true);
            $t = $to_coordinate['results'][0]['geometry']['location'];

            $from_coord = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $from . '&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $from_coordinate = json_decode($from_coord, true);
            $f = $from_coordinate['results'][0]['geometry']['location'];

            $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?&origins=' . $to . '&destinations=' . $from . '&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $distance_arr = json_decode($distance_data);
            return response()->json([
               "officeCoordinate" => $t,
               "clientCoordinate" => $f,
               "directionMatrix" => $distance_arr
            ]);
         break;
         case 48:
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
         break;
      }
      if ($request->handbook == "clientBusinessPoint") {
         $query = DB::table("CRM_SPR_BUSINESS_PLACE")
            ->get();
         return sprClientBusinessPoint::collection($query)->all();
      }
   }
   private function changeVisit($request){
      switch($request->action){
         case "setTime":
            $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
            ->where("ID", $request->propId)
            ->update([
               "TIME_MEETING" => $request->timeMeeting,
            ]);
         return response()->json([
            "$request->propId, set to time"
         ]);
         break;
         case "deleteClient":
            $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
            ->where("ID", $request->propId)
            ->delete();
         return response()->json([
            "$request->propId, delete"
         ]);
         break;
      }
   }
   //***УДАЛИТЬ */
   //Удалить нужно переписано на ClientAnalyticController от 
   private function profileClient($request){
      if ($request->action == "getMainInf") {
         $query = DB::table("CRM_CLIENT_INFO as cci")
            ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.ID", "cci.CLIENT_ID")
            ->select("cci.ID", "cci.ADDRESS", "NAME", DB::raw("CASE WHEN ccig.GUID IS NULL THEN 0 WHEN ccig.GUID IS NOT NULL THEN 1 END as guid"), "IIN_BIN", "CATO", "DEYATELNOST", "REGION", "DISTRICT")
            ->where("cci.ID", $request->clientId)
            ->get();
         return getMainInfCli::collection($query)->all();
      }
      if ($request->action == "getMngVisit") {
         $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES as cvtdp")
            ->leftJoin("CRM_VISIT_TO_DATE as cvtd", "cvtd.ID", "cvtdp.VISIT_ID")
            ->where("cvtdp.CLIENT_ID", $request->clientId)
            ->get();
         return response($query);
      }
      if ($request->action == "getContracts") {
         $query = DB::table("CRM_DOGOVOR as cd")
            ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
            ->where("ccig.ID", $request->clientId)
            ->get();
         if($query->isEmpty()) {
            return response()->json([
               "message" =>  "didn't contracts",
               "status" => false
            ]);
         } 
         else{
            if (count($query) > 15) {
               return getContracts::collection($query);
            } else {
               return getContracts::collection($query);
            }
         }
      }
      if ($request->action == "getLastContract") {
         $query = DB::table("CRM_CLIENT_ID_GUID as cig")
            ->select("cd.ID", "cd.NAIMENOVANIE", "cd.DATA_NACHALA_DEYSTVIYA", "cd.DATA_OKONCHANIYA_DEYSTVIYA", "cd.NOMER", "cd.STATUS", "cd.KONTRAGENT", "cd.MENEDZHER AS manager", "cd.SEZON", "cd.ADRES_DOSTAVKI", "cd.SUMMA_KZ_TG")
            ->join("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "cig.GUID")
            ->where("cig.ID", $request->clientId)
            ->get();
         return getLastContract::collection($query)->all();
      }
      if ($request->action == "getSubcides") {
         $query = DB::table("CRM_SHYMBULAK_SUBSIDIES as css")
            ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "css.APPLICANT_IIN_BIN")
            ->where("cci.ID", $request->clientId)
            ->get();
         return getSubcides::collection($query)->all();
      }
      if ($request->action == "setContacts") {
         $query = DB::table("CRM_CLIENT_CONTACTS")
            ->where("CLIENT_ID", $request->clientId)
            ->where("ID", $request->contactId);
         if ($request->position) {
            $query->update([
               "POSITION" => $request->position,
            ]);
         }
         if ($request->name) {
            $query->update([
               "NAME" => $request->name
            ]);
         }
         if ($request->phoneNumber) {
            $query->update([
               "PHONE_NUMBER" => $request->phoneNumber
            ]);
         }
         if ($request->email) {
            $query->update([
               "EMAIL" => $request->email
            ]);
         }
         return response()->json([
            "message" => "Records update",
            "status" => true
         ]);;
      }
      if ($request->action == "setMainInf") {
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
      if ($request->action == "getSuppMngr") {
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
      if ($request->action == "getBusinessPoint") {
         $query = DB::table("CRM_CLIENT_BUSINESS_PLACE as ccbp")
            ->select("ccbp.ID", "ccbp.CLIENT_ID", "ccbp.NAME", "ccbp.COORDINATE", "csbp.NAME as NAME_C")
            ->leftJoin("CRM_SPR_BUSINESS_PLACE as csbp", "csbp.ID", "ccbp.PLACE_ID")
            ->where("CLIENT_ID", $request->clientId)
            ->get();
         return getBusinessPoint::collection($query)->all();
      }
   }
   //до сюда

   private function UploadFiles($request){
        // Получаем файл
        $file = $request->file('file');
        // Генерируем уникальное имя для файла
        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $filePath =  '/uploads/' . $filename;
        $file->move(public_path('uploads'), $filename);
        switch($request->action){
            case "visitProfile":
               $type=$request->visitId;
               break;
            case "recomendation":
               $type=$request->visitId;
               break;
            default:
               $type = null;
               break;
        }
        // Сохраняем файл на диск
         $data = DB::table("CRM_UPLOADS_FILE")
         ->insertGetId([
            "filePath" => $filePath,
            "type" => $type
         ]);
         return response()->json([
            'success' => true,
            'fileId' => $data,
            'filePath' => 'http://10.200.100.17'.$filePath
         ], 
         200);
   }
   private function recomendationSurvey($request){
      $filePath = null;
      // Проверяем наличие файла
      if ($request->hasFile('file')) {
         // Получаем файл
         $file = $request->file('file');
         // Генерируем уникальное имя для файла
         $filename = uniqid() . '_' . $file->getClientOriginalName();
         $filePath =  '/uploads/' . $filename;
         // Сохраняем файл на диск
         $file->move(public_path('uploads'), $filename);
      }
      else{
         $filePath = null;
      }
      $query = DB::tabel()
      ->insert([
         "visitId" => $request->visitId,
         "questionId" => $request->recomendationId,
         "filePath" => $filePath,
         "value" => $request->value
      ]);
   // Если файл не найден, возвращаем ошибку
      return response()->json([
            'success' => false,
            'message' => 'Record Save',
      ], 400);
   }
}
