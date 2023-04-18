<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Resources\ProfieResource;

class ProfileControtroller extends Controller
{
    public function ProfileClusterFunc(Request $request){
        switch($request->action){
            case "getKpiUser":
                return $this->ProfileInfoTable($request);
                break;
        }
    }
    private function ProfileInfoTable(Request $request){
        $query = DB::table("CRM_KPI_BASIS")
        ->where("u.id", $request->id)
        ->leftJoin("CRM_USERS as cu", "cu.ID", "MANAGER_ID")
        ->leftJoin("users as u", "u.email", "cu.ADRES_E_P")
        ->get();
        $qq = collect($query)->where("PLAN", "!=", "100000000")->where("PLAN", ">", "0")->values();
        $qr = collect($query)->where("PLAN", "=", "100000000")->values();
        if(sizeof($qq)){
            return [
                "currentStep" => ProfieResource::collection($qq)->all(), 
                "passedStep" => ProfieResource::collection($qr)->all()
            ];
        }
        else{
            return response()->json([
                "message" => "don't data"
            ]);
            
        }
    }
}
