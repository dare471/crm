<?php

namespace App\Http\Controllers;

use App\Http\Resources\getLastPlanPayment;
use App\Http\Resources\regionBelong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserSettings;
use App\Http\Resources\webhookUserSettings;

class UserSettingsController extends Controller
{
    public function UserSettings(Request $request){
        if($request->settings == "getInfoUser"){
            $query = DB::table("users as u")
            ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
            ->where("u.id", $request->userId)
            ->first();
            return collect([
                "id" => $query->id,
                "name" => $query->NAIMENOVANIE,
                "direction" => $query->DIREKTSYA,
                "subDirection" => $query->PODRAZDELENIE,
                "workPosition" => $query->DOLZHNOST,
                "email" => $query->email,
                "supervisor" => $query->RUKOVODITELI_DIREKTSYI
            ]);
        }
        if($request->settings == "getLastPlanPayment"){
            if($request->userId == 1174){
                $userId = 1491;
            }
            else{
                $userId = $request->userId;
            }
            $manager_guid = $this->getGuidUser($userId);
            $converted_guid = bin2hex($manager_guid); // Преобразование строки в шестнадцатеричное представление
            $query = DB::select("EXECUTE L1.dbo.OPLATY_PO_GRAFIGU @manager_guid = 0x$converted_guid, @WEEKS = 2");
            $multiplied = collect($query)->mapWithKeys(function ($item, $key) {
                return [
                    $key => [
                        "ID" => $key,
                        "documentGuid" => $item->GUIDDOC,
                        "payment" => $item->OPLATA,
                        "sheduledAmount" => $item->SUMMA_PO_GRAFIGU,
                        "remainder" => $item->OSTATOK,
                        "docNumber" => $item->NOMER,
                        "season" => $item->SEZON,
                        "dateOnSchedule" => $item->DATA_PO_GRAFIKU,
                        "managerName" => $item->MENEDZHER,
                        "client" => $item->KONTRAGENT,
                        "clientId" => $item->clientId,
                        "weekNumber" => $item->NOMER_NEDELII,
                    ]
                ];
            });
            if($multiplied->isEmpty()){
                return $multiplied;
                // return response()->json([
                //     "message" => "not data, retry later"
                // ]);
            }
            else{
                return getLastPlanPayment::collection($multiplied)->values();
            }

        }
        if($request->settings == "subscribesRegion"){
            $queryUser = DB::table("users")
            ->where("ID", $request->userId)
            ->get();
            $userRegBilling = json_decode($queryUser[0]->region_belongs)->region;
            $query = DB::table("CRM_AISGZK_OBLAST_GEO")
            ->whereIn("KATO", $userRegBilling)
            ->get();
            return regionBelong::collection($query)->all();
        }
        if($request->settings == "notSubscribesRegion"){
            $queryUser = DB::table("users")
            ->where("ID", $request->userId)
            ->get();
            $userRegBilling = json_decode($queryUser[0]->region_belongs)->region;
            $query = DB::table("CRM_AISGZK_OBLAST_GEO")
            ->whereNotIn("KATO", $userRegBilling)
            ->get();
            return regionBelong::collection($query)->all();
        }
        if($request->settings == "subscribeToRegion" || $request->settings == "unSubscribeToRegion"){
            $queryUser = DB::table("users")
            ->where("ID", $request->userId)
            ->update(array("region_belongs" => json_encode($request->subscribeObj)));
            return response()->json([
                "message"=>"RecordUpdate"
            ]);
        }
        if($request->settings == "listClient"){
            $queryUser = DB::table("users as u")
            ->where("ID", $request->userId)
            ->get();
            $unclientId=json_decode($queryUser[0]->region_belongs)->region;
            $query = DB::table("CRM_CLIENT_INFO as cci")
            ->where("REGION", $unclientId);
            if($request->clientIin){
                $query->where("IIN_BIN", "LIKE", "$request->clientIin%"); 
            }
            return UserSettings::collection($query->get())->all();
        }
        if($request->settings == "listUnfollowedClient"){
            $queryUser = DB::table("users as u")
            ->where("ID", $request->userId)
            ->get();
            $unclientId=json_decode($queryUser[0]->unfollowClient)->clientId;
            $query = DB::table("CRM_CLIENT_INFO as cci")
            ->whereIn("ID", $unclientId);
            return UserSettings::collection($query->get())->all();
        }
        if($request->settings == "deletedUnfollowedClient" || $request->settings == "addUnfollowedClient"){
            $query = DB::table("users")
            ->where("id", $request->userId)
            ->update(array('unfollowClient' => json_encode($request->unfollowedObj)));
            return response()->json([
                "message"=>"Record updated"
            ]);
        }
        else{
            return response()->json([
                "message" => "get correctt settings type"
            ]);
        }
    }
    public function WebhookParametrs(Request $request){
        $query = DB::table("users")
        ->where("id", $request->userId)
        ->get();
        return webhookUserSettings::collection($query)->all();
    }
    private function getGuidUser($userId){
        return DB::table("users as u")
        ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
        ->where("u.id", $userId)
        ->value("cu.GUID as guidUser"); 
    }
}
