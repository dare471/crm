<?php

namespace App\Http\Controllers;

use App\Http\Resources\regionBelong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserSettings;
use App\Http\Resources\webhookUserSettings;

class UserSettingsController extends Controller
{
    public function UserSettings(Request $request){
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
    }
    public function WebhookParametrs(Request $request){
        $query = DB::table("users")
        ->where("id", $request->userId)
        ->get();
        return webhookUserSettings::collection($query)->all();
    }
}
