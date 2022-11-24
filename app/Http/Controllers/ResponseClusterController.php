<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AllContracts;

class ResponseClusterController extends Controller
{
    public  function ResponseFunction($request, $specificate){
        switch($request) {
            case empty($request->get(0)): 
                return response()->json([
                    "status" => 201,
                    "succes" => false,
                    "data" => 'not data'
                ]);
            break;
            case !empty($request->get(0)):
                case $request->get(0)->type == "managerContracts":
                    return response()->json([
                        "status" => 201,
                        "succes" => true,
                        "data" => AllContracts::collection($request)
                        ]);
                break;
                case $request[0]->type == "detailContract":
                    print_r($request, $specificate);
                    return response()->json([
                        "succes" => true,
                        "status" => 201,
                        "data" => $request,
                        "specificationContracts" => $specificate,
                    ]);
                break;
            break;
        }
    }
    public function ResponseHeadersFunction(){

    }
}
