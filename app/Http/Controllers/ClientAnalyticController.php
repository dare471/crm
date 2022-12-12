<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;
use App\Http\Resources\purchaseHistory;
use App\Http\Resources\areaStructure;
use App\Http\Resources\potentialCult;
use App\Http\Resources\purchaseHistoryMarket;
use App\Http\Resources\pivotYieldStructure;
use App\Http\Resources\pivotSubsideRegion;
use App\Http\Resources\pivotSubsideCountry;

class ClientAnalyticController extends Controller
{
   public function Analyse(Request $request){
    if($request->type == "getCountActive"){
        $query = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA as CDS")
        ->leftjoin("CRM_DOGOVOR as cd1", "cd1.GUID", "CDS.DOGOVOR_GUID")
        ->select("NOMENKLATURA as nomenclName", "cd1.SEZON as season", DB::raw("COUNT(NOMENKLATURA) as countProd"), DB::raw("SUM(CDS.SUMMA_KZ_TG) as summaProd"))
        ->whereIn("CDS.DOGOVOR_GUID", [DB::raw("(SELECT cd.GUID FROM CRM_DOGOVOR cd
        LEFT JOIN CRM_CLIENT_ID_GUID ccig ON ccig.GUID = cd.KONTRAGENT_GUID
        WHERE ccig.ID = $request->clientId)")])
        ->groupby("NOMENKLATURA", "NOMENKLATURA_GUID", "cd1.SEZON")
        ->orderByDesc("countProd")
        ->get();
        return $query;
   }
   if($request->type == "getPurchaseHistory"){
        $query = DB::table("CRM_SHYMBULAK_PIVOT_AA_PURCHASE_HISTORY as csph")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "csph.CORRECT_APPLICANT_IIN_BIN")
        ->where("cci.ID", $request->clientId)
        ->orderByDesc("YEAR")
        ->get();
        return response()->json([
            "status" =>  201,
            "success" => true,
            "data" => purchaseHistory::collection($query)
        ]);
   }
   if($request->type == "getAreaStructure"){
        $query = DB::table("CRM_SHYMBULAK_PIVOT_AREA_STRUCTURE as cspa")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspa.OWNER_IIN_BIN")
        ->where("cci.ID", $request->clientId)
        ->orderByDesc("YEAR")
        ->get();
        return response()->json([
            "status" =>  201,
            "success" => true,
            "data" => areaStructure::collection($query)
        ]);
   }
   if($request->type == "getPotentialCult"){
        $query = DB::table("CRM_SHYMBULAK_PIVOT_POTENTIAL_OF_CULTURE as cspp")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspp.IIN_BIN")
        ->where("cci.ID", $request->clientId)
        ->get();
        return response()->json([
            "status" =>  201,
            "success" => true,
            "data" =>  potentialCult::collection($query)
        ]);
   }
   if($request->type == "getPurchaseHistoryMarket"){
        $query = DB::table("CRM_SHYMBULAK_PIVOT_PURCHASE_HISTORY_BY_MARKET as cspphm")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspphm.CORRECT_APPLICANT_IIN_BIN")
        ->where("cci.ID", $request->clientId)
        ->orderByDesc("YEAR")
        ->get();
        return response()->json([
            "status" =>  201,
            "success" => true,
            "data" => purchaseHistoryMarket::collection($query)
        ]);
    }
    if($request->type == "getPivotYieldStructure"){
        $query = DB::table("CRM_SHYMBULAK_PIVOT_YIELD_STRUCTURE as cspys")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspys.OWNER_IIN_BIN")
        ->where("cci.ID", $request->clientId)
        ->orderByDesc("YEAR")
        ->get();
        return response()->json([
            "status" =>  201,
            "success" => true,
            "data" =>  pivotYieldStructure::collection($query)
        ]);
    }
    if($request->type == "getPivotSubsideRegion"){
        $query = DB::table("CRM_SHYMBULAK_SUBSIDIES_PIVOT as cssp")
        ->where("cssp.CATO", "like", "".$request->regionId."%")
        ->orderByDesc("SEASON")
        ->get();
        return response()->json([
            "status" =>  201,
            "success" => true,
            "data" => pivotSubsideRegion::collection($query)
        ]);
    }
    if($request->type == "getPivotSubsideCountry"){
        $query = DB::table("CRM_SHYMBULAK_SUBSIDIES_PIVOT_KZ")
        ->orderByDesc("SEASON")
        ->get();
        return response()->json([
            "status" =>  201,
            "success" => true,
            "data" => pivotSubsideCountry::collection($query)
        ]);
    }
    }
}