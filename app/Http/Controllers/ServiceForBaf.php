<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ResourceForBaf;

class ServiceForBaf extends Controller
{
    public function MethodControllers(Request $request){
        switch($request->type){
            case "getClientInf":
              return $this->SwitchControllersAction($request);
                break;
        }
    }
    private function SwitchControllersAction(Request $request){
        switch($request->action){
            case "withBin":
            return $this->getMainInf($request);
                break;
        }
    }
    private function getMainInf($request){
        $query = DB::table("CRM_CLIENT_INFO")
        ->where("IIN_BIN", $request->bin)
        ->get();
        return ResourceForBaf::collection($query)->first();
    }
}
