<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmulationRespController extends Controller
{
    public function responseErp(Request $request){
        if($request->action == "sendErp"){
        $query = DB::table("CRM_VISIT_TO_DATE")
            ->whereIn("ID", $request->visitId)
            ->update([
                "STATUS" => 2
            ]);
        $subQuery = DB::table("CRM_VISIT_TO_DATE as cvtd")
        ->leftJoin("CRM_SPR_STATUS as cps", "cps.ID", "cvtd.STATUS")
        ->where("ID", $request->visitId)
        ->get();
            return response()->json([
                'success' => true,
                'status' => 201,
                'visit' =>$subQuery
            ]);
        }
        else{
            return response()->json([
                'message' => 'Error type'
            ]);
        }
    }
}
