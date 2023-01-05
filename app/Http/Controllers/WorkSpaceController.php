<?php

namespace App\Http\Controllers;

use App\Http\Resources\clientLRegion;
use App\Http\Resources\clientInf;
use App\Http\Resources\managerRelation;
use App\Http\Resources\contractRelation;
use App\Http\Resources\managerContract;
use App\Http\Resources\addicionalContract;
use App\Http\Resources\contractHead;
use App\Http\Resources\specificationContracts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkSpaceController extends Controller
{
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
         'Поступление_2021 AS ADMISSION_2021',
         'Поступление_2022 AS ADMISSION_2022')
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
         ->whereIn("SEZON", ['Сезон 2022', 'Сезон 2021'])
         ->get();
         return response($query);
      }
   }  
}
