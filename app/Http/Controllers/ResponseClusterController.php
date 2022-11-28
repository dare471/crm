<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AllContracts;
use App\Http\Resources\RegionPolygonResource;
use App\Http\Resources\DistrictPolygonResource;
use App\Http\Resources\ClientsFieldsPolygonResource;
use App\Http\Resources\GetClientLandsResource;
use App\Http\Resources\ClientFieldsPolygonResource;
use App\Http\Resources\ElevatorListResource;
use App\Http\Resources\CultureRegionResource;
use App\Http\Resources\HeaderClientLandResource;
use App\Http\Resources\HeaderClientsCulturesResource;
use App\Http\Resources\ClientFieldsGetCultureResource;
use App\Http\Resources\HeaderLandInfResource;
use App\Http\Resources\FilterMaps;
use App\Http\Resources\FilterSprCultMaps;


class ResponseClusterController extends Controller
{
    public  function ResponseFunction($request, $header){
        if($request->get(0)){
        switch($request->get(0)->type){
            case "region": 
                return response()->json([
                    'succes' => true,
                    'status' => 201,
                    'data' => RegionPolygonResource::collection($request)
                ]);
            break;
            case "district": 
                return response()->json([
                    'succes' => true,
                    'status' => 201,
                    'data' => DistrictPolygonResource::collection($request)
                ]);
            break;
            case "clientLands":
                return response()->json([
                    'succes' => true,
                    'status' => 201,
                    'data' => GetClientLandsResource::collection($request) //ClientsFieldsPolygonResource::collection($request)
                ]);
            break;
            case "elevator":
                return response()->json([
                    'succes' => true,
                    'status' => 201,
                    'data' => ElevatorListResource::collection($request)
                ]);
            break;
            case "getCulture":
                return response()->json([
                    'success' => true,
                    'status' => 201,
                    'data' => CultureRegionResource::collection($request)
                ]);
            break;
            case "clientLand":
                return response()->json([
                    'success' => true,
                    'status' => 201,
                    'header' =>  HeaderClientLandResource::collection($header), 
                    'data' =>  ClientFieldsPolygonResource::collection($request)
                ]);
            break;
            case "clientCulture":
                return response()->json([
                    'success' => true,
                    'status' => 201,
                    'header' => HeaderClientsCulturesResource::collection($header), 
                    'data' =>  ClientsFieldsPolygonResource::collection($request)
                ]);
            break;
            case "landInf":
                return response()->json([
                    'success' => true,
                    'status' => 201,
                    'header' =>  HeaderLandInfResource::collection($header), 
                    'data' =>  ClientFieldsGetCultureResource::collection($request)
                ]);
            break;
            // case "seedPivot": 
            //     return response()->json([
            //         "status" => 201,
            //         "succes" => true,
            //         "data" => $request->current_page
            //         ]);
            // break;
            case "mineralPivot": 
                return response()->json([
                    "status" => 201,
                    "succes" => true,
                    "data" => $request
                    ]);
            break;
            case "pesticidePivot": 
                return response()->json([
                    "status" => 201,
                    "succes" => true,
                    "data" => $request
                    ]);
            break;
            case "managerContracts":
                return response()->json([
                    "status" => 201,
                    "succes" => true,
                    "data" => AllContracts::collection($request)
                    ]);
            break;
            case "detailContract":
                return response()->json([
                    "succes" => true,
                    "status" => 201,
                    "data" => $request,
                    "specificationContracts" => $header,
                ]);
            break;
            default: 
                return response()->json([
                    "status" => 201,
                    "succes" => false,
                    "data" => "Missing data, correct 'type' or error in request body"
                ]);
            }
        }
        else{
            return response()->json([
                "succes" => false,
                "data" => "Missing data, correct 'type' or error in request body"
            ]);
        }
    }
    public function ResponseHeadersFunction(){

    }
}
