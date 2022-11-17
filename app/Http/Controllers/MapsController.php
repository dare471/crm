<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\RegionPolygonResource;
use App\Http\Resources\DistrictPolygonResource;
use App\Http\Resources\ClientFieldsPolygonResource;
use App\HTTP\Resources\ElevatorListResource;


class MapsController extends Controller
{
    public function MainController(Request $request)
    {
        if($request->type=='region'){
            $query = DB::table("CRM_AISGZK_OBLAST_GEO")
            ->select(DB::raw("'region' as type"), 
            "ID", 
            "NAME", 
            DB::raw("SUBSTRING(KATO, 0, 3) as cato"),
            "POPULATION_AREA as population_area",
            "POPULATION_CITY as population_city",
            "POPULATION_VILLAGE as population_village",
            "GEOMETRY_RINGS as  geometry_rings",
            "KLKOD" 
            )
            ->get();
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => RegionPolygonResource::collection($query)
            ]);
        }
        if($request->type=='district'){
            $query = DB::table("CRM_AISGZK_RAION_GEO")
            ->select(
            DB::raw("'district' as type"),
            "TEXT",
            "KLKOD",
            "VNAIM", 
            DB::raw("SUBSTRING(KATO, 0, 5) as cato"), 
            "geometry_rings")
            ->where("KATO", "LIKE", "$request->cato%")
            ->get();
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => DistrictPolygonResource::collection($query)
            ]);
        }
        if($request->type=='clientFields'){
            $region_cato=substr($request->cato, 0, 2);
            $district_cato = substr($request->cato, 2, 4);
            $condition_arr = [["CCI.REGION", $region_cato]];
            if($district_cato){
                $condition_arr[] = ["CCI.DISTRICT", $district_cato];
            }
            $query = DB::table("CRM_CLIENT_PROPERTIES as CCP")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCP.CLIENT_INFO_ID")
            ->leftjoin("CRM_CLIENT_ID_GUID as CCIG", "CCIG.ID", "CCI.CLIENT_ID")
            ->select(
               DB::raw("'clientFields' as type"),
                "CCP.ID",
                DB::raw("CASE WHEN CCIG.GUID IS NULL THEN ''
                WHEN CCIG.GUID IS NOT NULL THEN '1'
                END as guid"),
                "CCI.NAME as name",
                "CLIENT_INFO_ID as client_info_id",
                "CCP.COORDINATES as geometry_rings"
                )
            ->where($condition_arr)
            ->get();
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => ClientFieldsPolygonResource::collection($query)
            ]);
        }
        if($request->type=='elevatorMarker'){
            $query = DB::table("CRM_ELEVATOR")
            ->select("ID",
            "NAME", 
            "BIN", 
            "LOCATION", 
            "STATION", 
            "BIN", 
            "CONTACTS", 
            "STORAGE_VOLUME", 
            "LATITUDE", 
            "LONGITUDE")
            ->get();
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => ElevatorListResource::collection($query)
            ]);
        }
    }
    public function ClientFields(){

        

    }
}


