<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkSpaceController extends Controller
{
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
}
