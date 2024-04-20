<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\dashboard\SalesManagerMain;
use App\Http\Resources\dashboard\SalesManagerPlanFactGrouped;
use App\Http\Resources\dashboard\SalesManagerPlanFactPeriodYear;
use App\Http\Resources\dashboard\SalesManagerContract;

class DashboardController extends Controller
{
      public function __construct()
    {
        $this->middleware('auth:api');
    }
    protected function guard() {
        return Auth::guard();
    }
    public function RouteCase(Request $request){
        switch($request->type){
            case "getPlanFactUser":
                return $this->planFactUser($request);
            case "getPlanFactUserGroup":
                return $this->planFactUserGroup($request);
            case "getExecutePlan":
                return $this->executePlan($request);
            case "getContract":
                return $this->contract($request);
            default:
                return collect(["message"=> "correct send type method"], 204);
            }
    }
    private function manageridentification($role){
        // Проверяем, установлен ли role
        if(is_null($role)){
            return 8; 
        }
        else{
            return $role;
        }
    }
    
    private function planFactUser($request){
        $query = DB::select("EXEC CRM_DWH_new.db_datareader.PLAN_FACT @SEZON='Сезон 2024',@USER_ID=".$this->manageridentification($request->role).";");
        return SalesManagerMain::collection($query)->all();
    }

    private function planFactUserGroup($request){
   
        $query = DB::select("EXEC CRM_DWH_new.db_datareader.PLAN_FACT_GROUPED @SEZON='Сезон 2024',@USER_ID=".$this->manageridentification($request->role).";");
        return SalesManagerPlanFactGrouped::collection($query)->all();
    }

    private function executePlan($request){
        $query = DB::select("EXEC CRM_DWH_new.db_datareader.ISPLONENIE_PLANA_PO_MESYATSAM @USER_ID=".$this->manageridentification($request->role).";");
        return SalesManagerPlanFactPeriodYear::collection($query)->all();
    }

    private function contract($request){
        $query = DB::select("EXEC CRM_DWH_new.db_datareader.DOGOVORY @SEZON='Сезон 2024',@USER_ID=".$this->manageridentification($request->role).";");
        return SalesManagerContract::collection($query)->all();
    }
}
