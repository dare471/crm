<?php

namespace App\Http\Controllers;

use App\Http\Resources\getAnalyticsForClient;
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;

class MapsAnalyticsController extends Controller
{
    public function AnalyticsMaps(Request $request){
        if($request->type == "seedsPivot"){
                $query = DB::table("CRM_SHYMBULAK_SEEDS as CSS")
                ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.IIN_BIN", "CSS.APPLICANT_IIN_BIN")
                ->leftjoin("CRM_CLIENT_PROPERTIES as CCP", "CCP.CLIENT_INFO_ID", "CCI.ID")
                ->leftjoin("CRM_SPR_CULTURE as CSC", "CSC.ID", "CCP.CULTURE")
                ->select(DB::raw("'seedPivot' as type"),"CSC.NAME as cultName", 
                "CSC.ID as cultId", 
                "CSS.PROVIDER_NAME as providerName", 
                DB::raw("SUM(SUM_SUBSIDIES) as sumSubside"), 
                DB::raw("SUM(AREA) as areaSubs"),
                DB::raw("SUM(CSS.VOLUME) as volume"),
                "CSS.UNIT as unit",
                DB::raw("SUM(ROUND(USAGE_AREA, 0)) as usageArea")
                )
                ->where("CSS.YEAR", "2022");
                if($request->regionId){
                    $query->where("CCI.REGION", $request->regionId);
                }
                if($request->districtId){
                    $query->where("CCI.DISTRICT", $request->districtId);
                }
                if($request->cultureId){
                    $query->where("CSC.ID", $request->cultureId);
                }
                $query = $query->groupBy("CSC.NAME", "CSC.ID", "CSS.PROVIDER_NAME", "CSS.UNIT")->orderByDesc("sumSubside")->paginate(10);
                
                return response()->json([
                        "status" => 201,
                        "succes" => true,
                        "header" => [
                            "currentPage" => $query->currentPage(),
                            "nextPageUrl" => $query->nextPageUrl(),
                            "prevPageUrl" => $query->previousPageUrl(),
                            "total" => $query->total()
                        ],
                        "data" => $query->items()
                ]);
                return (new ResponseClusterController)->ResponseFunction($query, null);
              
        }
        if($request->type == "mineralsPivot"){
            $query = DB::table("CRM_SHYMBULAK_MINERALS as CSM")
                ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.IIN_BIN", "CSM.APPLICANT_IIN_BIN")
                ->leftjoin("CRM_CLIENT_PROPERTIES as CCP", "CCP.CLIENT_INFO_ID", "CCI.ID")
                ->leftjoin("CRM_SPR_CULTURE as CSC", "CSC.ID", "CCP.CULTURE")
                ->select(DB::raw("'mineralPivot' as type"),"CSC.NAME as cultName", 
                "CSC.ID as cultId", 
                "CSM.MINERALS_NAME as name",
                "CSM.PROVIDER_NAME as providerName", 
                DB::raw("SUM(SUM_SUBSIDIES) as sumSubside"), 
                DB::raw("SUM(AREA) as areaSubs"),
                DB::raw("SUM(CSM.VOLUME) as volume"),
                "CSM.UNIT as unit",
                DB::raw("SUM(ROUND(USAGE_AREA, 0)) as usageArea")
                )
                ->where("CSM.APP_NUMBER", "LIKE",  "%2022%");
                if($request->regionId){
                    $query->where("CCI.REGION", $request->regionId);
                }
                if($request->districtId){
                    $query->where("CCI.DISTRICT", $request->districtId);
                }
                if($request->cultureId){
                    $query->where("CSC.ID", $request->cultureId);
                }
                $query = $query->groupBy("CSC.NAME", "CSM.MINERALS_NAME", "CSC.ID", "CSM.PROVIDER_NAME", "CSM.UNIT")->orderByDesc("sumSubside")->paginate(10);
                return response()->json([
                    "status" => 201,
                    "succes" => true,
                    "header" => [
                        "currentPage" => $query->currentPage(),
                        "nextPageUrl" => $query->nextPageUrl(),
                        "prevPageUrl" => $query->previousPageUrl(),
                        "total" => $query->total()
                    ],
                    "data" => $query->items()
            ]);
               // return (new ResponseClusterController)->ResponseFunction($query, null);

        }
        if($request->type == "pesticidesPivot"){
            $query = DB::table("CRM_SHYMBULAK_PESTICIDES as CSP")
                ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.IIN_BIN", "CSP.APPLICANT_IIN_BIN")
                ->leftjoin("CRM_CLIENT_PROPERTIES as CCP", "CCP.CLIENT_INFO_ID", "CCI.ID")
                ->leftjoin("CRM_SPR_CULTURE as CSC", "CSC.ID", "CCP.CULTURE")
                ->select(DB::raw("'pesticidePivot' as type"),"CSC.NAME as cultName", 
                "CSC.ID as cultId", 
                "CSP.PESTICIDES_NAME as name",
                "CSP.PROVIDER as providerName", 
                DB::raw("SUM(SUM_SUBSIDIES) as sumSubside"), 
                DB::raw("SUM(AREA) as areaSubs"),
                DB::raw("SUM(CSP.VOLUME) as volume"),
                "CSP.UNIT as unit",
                DB::raw("SUM(ROUND(USAGE_AREA, 0)) as usageArea")
                )
                ->where("CSP.APP_NUMBER", "LIKE",  "%2022%");
                if($request->regionId){
                    $query->where("CCI.REGION", $request->regionId);
                }
                if($request->districtId){
                    $query->where("CCI.DISTRICT", $request->districtId);
                }
                if($request->cultureId){
                    $query->where("CSC.ID", $request->cultureId);
                }
                $query = $query->groupBy("CSC.NAME", "CSP.PESTICIDES_NAME", "CSC.ID", "CSP.PROVIDER", "CSP.UNIT")->orderByDesc("sumSubside")->paginate(10);

                return response()->json([
                    "status" => 201,
                    "succes" => true,
                    "header" => [
                        "currentPage" => $query->currentPage(),
                        "nextPageUrl" => $query->nextPageUrl(),
                        "prevPageUrl" => $query->previousPageUrl(),
                        "total" => $query->total()
                    ],
                    "data" => $query->items()
                ]);
                //return (new ResponseClusterController)->ResponseFunction($query, null);

        }
        if($request->type == "getAnalyticsForClient"){
            $query = DB::table("CRM_CLIENT_INFO as CCI")
            ->select(DB::raw("SUM(CSS.SUM_SUBSIDIES) as sumSubsClient"), DB::raw("(SELECT SUM(CSS2.SUM_SUBSIDIES) as SUMSUBCIDES FROM CRM_CLIENT_INFO CCI2
            LEFT JOIN CRM_SHYMBULAK_SEEDS CSS2 ON CSS2.APPLICANT_IIN_BIN=CCI2.IIN_BIN
            WHERE CCI2.ID NOT IN ($request->clientId) AND CCI2.REGION = CCI.REGION and CSS2.YEAR = CSS.YEAR
            GROUP BY CSS2.YEAR) as difSumSubcides"), "YEAR", "SEEDS_NAME", "CCI.REGION", DB::raw("SUM(VOLUME) as sumVolumeClient"), DB::raw("(SELECT SUM(CSS2.VOLUME) as volume FROM CRM_CLIENT_INFO CCI2
            LEFT JOIN CRM_SHYMBULAK_SEEDS CSS2 ON CSS2.APPLICANT_IIN_BIN=CCI2.IIN_BIN
            WHERE CCI2.ID NOT IN ($request->clientId) AND CCI2.REGION = CCI.REGION and CSS2.YEAR = CSS.YEAR
            GROUP BY CSS2.YEAR) as difSumVolume"),  "CULTURE")
            ->leftJoin("CRM_SHYMBULAK_SEEDS as CSS", "CSS.APPLICANT_IIN_BIN", "CCI.IIN_BIN")
            ->where("CCI.ID", $request->clientId)
            ->groupBy("YEAR", "SEEDS_NAME",  "CULTURE", "CCI.REGION")
            ->get();

            return getAnalyticsForClient::collection($query)->all();
        }
    }
}
