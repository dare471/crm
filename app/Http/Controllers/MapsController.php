<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientFieldsGetCultureResource;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\RegionPolygonResource;
use App\Http\Resources\DistrictPolygonResource;
use App\Http\Resources\ClientsFieldsPolygonResource;
use App\Http\Resources\ClientFieldsPolygonResource;
use App\Http\Resources\ElevatorListResource;
use App\Http\Resources\CultureRegionResource;
use App\Http\Resources\FilterMaps;
use App\Http\Resources\FilterSprCultMaps;

use function PHPSTORM_META\map;

class MapsController extends Controller
{

//* Контроллер для первых слоей на карте Область и Районы
    public function MainController(Request $request)
    {
        if($request->type=='region'){
            $query = DB::table("CRM_AISGZK_OBLAST_GEO")
            ->select(DB::raw("'region' as type"), 
            "ID", 
            "NAME", 
            DB::raw("SUBSTRING(KATO, 0, 3) as region"),
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
            DB::raw("SUBSTRING(KATO, 0, 5) as district"), 
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
                "CLIENT_INFO_ID as clientID",
                "CCP.COORDINATES as geometry_rings"
                )
            ->where($condition_arr)
            ->get();
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => ClientsFieldsPolygonResource::collection($query)
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
        if($request->type=='getCulture'){
            if($request->typeLevel == 'region'){
                $typeLevel = 'CSC.REGION';
            }
            if($request->typeLevel == 'district'){
                $typeLevel = 'CSC.DISTRICT';
            }
            $query = DB::table("CRM_SPR_CULTURE as CSC")
            ->leftjoin("CRM_CLIENT_PROPERTIES as CCP", "CCP.CULTURE", "CSC.ID")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCP.CLIENT_INFRO_ID")
            ->select("CSC.ID","CSC.NAME")
            ->where($typeLevel, $request->regionCato)
            ->groupBy("CSC.ID", "CSC.NAME")
            ->get();
            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => CultureRegionResource::collection($query)
            ]);
        }
    }
//* Конец функции

//* Вывод участков клиентов
    public function MapsClient(Request $request){

        if($request->type == 'allFields'){
            $query = DB::table("CRM_CLIENT_PROPERTIES as CCR")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCR.CLIENT_INFO_ID")
            ->leftjoin("CRM_CLIENT_ID_GUID as CCIG", "CCIG.ID", "CCI.CLIENT_ID")
            ->select(
            "CCR.ID as id",
            "FIELDS as fields",
            "CLIENT_INFO_ID as client_info_id",
            DB::raw("CASE 
                WHEN CCIG.GUID IS NULL THEN NULL
                WHEN CCIG.GUID IS NOT NULL THEN '1'
                END as guid
            "),
            "CCR.COORDINATES as geometry_rings",
            "AREA as area"
            )
            ->where("CLIENT_INFO_ID", $request->clientID)
            ->get();

            $headers = DB::table("CRM_CLIENT_PROPERTIES as CCR")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCR.CLIENT_INFO_ID")
            ->leftjoin("CRM_CLIENT_ID_GUID as CCIG", "CCIG.ID", "CCI.CLIENT_ID")
            ->leftjoin("CRM_SPR_CULTURE as CSC", "CSC.ID", "CCR.CULTURE")
            ->select(DB::raw("'clientLandInf' as type"), 
            "CLIENT_INFO_ID as clientID",
            DB::raw("COUNT(*) as countLands"),
            DB::raw("SUM(AREA)/10000 as area")
            )
            ->where("CLIENT_INFO_ID", $request->clientID)
            ->groupby("CLIENT_INFO_ID")
            ->get();
            $response = ClientFieldsPolygonResource::collection($query);
        }

        if($request->type == 'cultures'){
            $query = DB::table("CRM_CLIENT_PROPERTIES as CCR")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCR.CLIENT_INFO_ID")
            ->leftjoin("CRM_CLIENT_ID_GUID as CCIG", "CCIG.ID", "CCI.CLIENT_ID")
            ->leftjoin("CRM_SPR_CULTURE as CSC", "CSC.ID", "CCR.CULTURE")
            ->select(
            'CSC.NAME as nameCult',
            'CULTURE as fieldsCultureId',
            'CCR.CLIENT_INFO_ID as client_info_id',
            'CSC.COLOR as color',
            DB::raw("geometry_rings=(SELECT STRING_AGG(COORDINATES, ' | ')  FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] CCP WHERE CCP.CLIENT_INFO_ID =CCR.CLIENT_INFO_ID AND CCP.CULTURE =CCR.CULTURE)")
            )
            ->where("CLIENT_INFO_ID", $request->clientID)
            ->where("SOURCE", "1")
            ->groupby("CSC.NAME", "CULTURE", "CCR.CLIENT_INFO_ID", "CSC.COLOR")
            ->get();
            $response = ClientsFieldsPolygonResource::collection($query);

            $headers = DB::table("CRM_CLIENT_PROPERTIES as CCR")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCR.CLIENT_INFO_ID")
            ->select(DB::raw("SUM(AREA) as area_g", 
            "COUNT(*) as countFields"), 
            "CCI.NAME as name", 
            "CCI.ADDRESS as address")
            ->where("CCR.CLIENT_INFO_ID", $request->clientID)
            ->groupBy("CCI.NAME", "CCI.ADDRESS")
            ->get();
            //$response = ClientsFieldsPolygonResource::collection($query);
        }

        if($request->type == 'getFieldsCulture'){
            $query = DB::table("CRM_CLIENT_PROPERTIES as CCR")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCR.CLIENT_INFO_ID")
            ->leftjoin("CRM_CLIENT_ID_GUID as CCIG", "CCIG.ID", "CCI.CLIENT_ID")
            ->leftjoin("CRM_SPR_CULTURE as CSC", "CSC.ID", "CCR.CULTURE")
            ->select("CCR.ID", 
            "FIELDS", 
            "CCR.CLIENT_INFO_ID", 
            "CSC.COLOR as color",
            "CSC.NAME",
            DB::raw("CASE 
            WHEN CCIG.GUID IS NOT NULL THEN '1'
            WHEN CCIG.GUID IS NULL THEN NULL
            END AS guid"), 
            "CCR.COORDINATES", 
            "CULTURE", 
            DB::raw("CONCAT(AREA/10000, 'Га') as area"))
            ->where("CLIENT_INFO_ID", $request->clientID)
            ->where("CCR.CULTURE", $request->fieldsCultureID)
            ->where("SOURCE", 1)
            ->get();
           $response = ClientFieldsGetCultureResource::collection($query);

            $headers =  DB::table("CRM_CLIENT_PROPERTIES AS CCR")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCR.CLIENT_INFO_ID")
            ->select(DB::raw("SUM(AREA/10000) as area"), 
            DB::raw("COUNT(*) as count_fields"))
            ->where("CLIENT_INFO_ID", $request->clientID)
            ->where("CCR.CULTURE", $request->fieldsCultureID)
            ->get();
        }
        return response()->json([
                'success' => true,
                'status' => 201,
                'headers' => $headers,
                'data' => $response
            ]);
        }
//*Конец функции 

//* ФИЛЬТР участков под критерий  
        public function FilterForMaps(Request $request){
                if($request->type == "sprCult"){
                    $region = substr($request->district, 0, 2);
                    $district = substr($request->district, 2, 4);
                    $query = DB::table("CRM_SPR_CULTURE as CSC")
                    ->leftjoin("CRM_CLIENT_PROPERTIES as CCR", "CCR.CULTURE", "CSC.ID")
                    ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCR.CLIENT_INFO_ID")
                    ->select(
                        "CSC.ID as id",
                        "CSC.NAME as nameCult"
                        )
                    ->where("CCI.REGION", $region)
                    ->where("CCI.DISTRICT", $district);
                    
                    $response = FilterSprCultMaps::collection($query->groupBy("CSC.ID", "CSC.NAME")->get());
                }
                if($request->type == "searchIin"){
                    $region = substr($request->catoID, 0, 2);
                    $district = substr($request->catoID, 2, 4);
                    $query = DB::table("CRM_CLIENT_INFO as CCI")
                    ->leftjoin("CRM_CLIENT_PROPERTIES AS CCP", "CCP.CLIENT_INFO_ID", "CCI.ID")
                    ->leftjoin("CRM_SPR_CULTURE as CSC", "CSC.ID", "CCP.CULTURE")
                    ->select("CCI.ID as clientId", "CCI.NAME as clientName", "CCI.IIN_BIN as clientIin")
                    ->where("CCI.IIN_BIN", "LIKE", "$request->clientIin%")
                    ->where("CCI.REGION", $region)
                    ->where("CCI.DISTRICT", $district);
                    if($request->culture){
                        $query->whereIn("CCP.CULTURE", $request->culture);
                    }
                    return response()->json([
                        "succees" => true,
                        "status" => 201,
                        "data" => $query->groupBy("CCI.ID", "CCI.NAME", "CCI.IIN_BIN")->get()
                    ]);
                }
                if($request->type == "filterMaps"){
                $query = DB::table("CRM_CLIENT_PROPERTIES as CCR")
                ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.ID", "CCR.CLIENT_INFO_ID")
                ->leftjoin("CRM_CLIENT_ID_GUID as CCIG", "CCIG.ID", "CCI.CLIENT_ID")
                ->leftjoin("CRM_SPR_CULTURE as CSC", "CSC.ID", "CCR.CULTURE")
                ->select(
                    DB::raw("'clientLands' as type"), 
                    "CCR.ID as fieldsID", 
                    "CLIENT_INFO_ID as clientID", 
                    "CCI.NAME as clientName",
                    "CCI.IIN_BIN",
                    DB::raw("CASE WHEN CCIG.GUID IS NULL THEN NULL WHEN CCIG.GUID IS NOT NULL THEN '1' END as guid"), 
                    "CCR.CULTURE as cultureID", 
                    "CSC.NAME as cultureName",
                    "CSC.COLOR as color",
                    "CCI.DISTRICT as district",
                    "CCI.REGION as region",
                    "CCR.COORDINATES as geometry_rings"
                );
                if($request->cato){
                    $region = substr($request->cato, 0, 2);
                    $district = substr($request->cato, 2, 4);
                    $query->where("CCI.REGION", $region)
                    ->where("CCI.DISTRICT", $district);
                }
                if($request->cult){
                    $query->whereIn("CCR.CULTURE", $request->cult);
                }
                if($request->clientId){
                    $query->where("CLIENT_INFO_ID", $request->clientId);
                }        
                $response = FilterMaps::collection($query->get());
            }
            if(empty($query)){
                return response()->json([
                    "succees" => true,
                    "status" => 201,
                    "message" => "error type!"
                ]);
            }
            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $response
            ]);
        }
//* Конец функции
}
               


