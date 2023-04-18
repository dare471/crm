<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AllContracts;
use Illuminate\Support\Facades\DB;
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
                return RegionPolygonResource::collection($request)->all();
            break;
            case "regionBilling": 
                return RegionPolygonResource::collection($request)->all();
            break;
            case "district": 
                return DistrictPolygonResource::collection($request)->all();
            break;
            case "clientLand":
                return GetClientLandsResource::collection($request)->all(); //ClientsFieldsPolygonResource::collection($request)
            break;
            case "elevator":
                return ElevatorListResource::collection($request)->all();
            break;
            case "getCulture":
               return CultureRegionResource::collection($request)->all();
            break;
            case "clientLandPlot":
               return ClientFieldsPolygonResource::collection($request)->all();
            break;
            case "clientCulture":
                return ClientsFieldsPolygonResource::collection($request)->all();
            break;
            case "landInf":
                return ClientFieldsGetCultureResource::collection($request)->all();
            break;
            case "managerContracts":
                return  AllContracts::collection($request)->all();
            break;
            case "detailContract":
                 return $request;
            break;
            default: 
                return response()->json([
                    "status" => 201,
                    "succes" => false,
                    "data" => "Missing data, correct 'type' or error in request body"]);
            }
        }
        else{
            return response()->json([
                "succes" => false,
                "data" => "Missing data, correct 'type' or error in request body"
            ]);
        }
    }
    public function ResponseHeadersFunction(Request $request){
        switch($request->action){
            case "uploadNewApk":
                return $this->uploadNewApk($request);
                break;
            case "getNewVersion":
                return $this->getNewVersion($request);
                break;
            default: 
                return collect([
                    "message" => "correct type !!"
                ]);
        }
    }
    private function getNewVersion($request){
        $qq = DB::table("CRM_MOBILE_VERSION")
        ->orderByDesc("id")
        ->limit(1)
        ->get();
        return $qq->first();
        // return $qq->map(function ($item){
        //     collect([
        //         "id" => $item->id,
        //         "version" => $item->version,
        //         "url" => $item->url
        //     ])->first();
        // });  
    }
    private function uploadNewApk(Request $request){
        $file = $request->file('file');
        // Генерируем уникальное имя для файла
        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        $version = DB::table("CRM_MOBILE_VERSION")
        ->insert([
            "version" => $request->versionName,
            "url" => 'https://crm.aleamgro.com:8080/uploads/' . $filename
        ]);
        return collect([
            "message" => "file upload",
            "status" => true
        ]);
    }
}
