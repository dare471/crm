<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function AnalyticsMaps(Request $request){
        if($request->type == "mapAnalyses"){
            $query = DB::table("")
            ->select("")
            ->get();
        }
        if($request->type == ""){

        }
    }
}
