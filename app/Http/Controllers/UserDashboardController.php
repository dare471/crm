<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserDashboard;

class UserDashboardController extends Controller
{
   public function RouteCase(Request $request){
    switch($request->type){
        case "planFactUser":
            return $this->planFactUser($request);
        default:
            return collect(["message"=> "correct send type method"], 204);
        }
        
   }
   private function planFactUser($request){
        $query = DB::select("EXEC db_datareader.PLAN_FACT_GROUPED @SEZON='Сезон 2023',@USER_ID=$request->userId;");
        return UserDashboard::collection($query);
   }
}
