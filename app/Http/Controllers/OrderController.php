<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function LogicController(Request $request){
        switch($request->action){
            case "getMyOrder":
                return $this->getMyorder($request->userId);
                break;
            case "getMyWareHouse":
                return $this->getMyWareHouse($request->userId);
                break;
            case "getMyProduct":
                return $this->getMyProduct($request->userId);
                break;
            default:
                return collect(["message"=> "Please, Send Correct Action"]);
        }
    }
    private function getMyOrder($request){
        $query = DB::table("users as u")
        ->leftJoin("CRM_USERS as cu", "cu.ADRES_E_P", "u.email")
        ->leftJoin("CRM_DOGOVOR as cd", "cd.MENEDZHER_GUID", "cu.GUID")
        ->where("u.ID", 1463)
        ->where("SEZON", "Сезон 2023")
        ->get();
        return $query->map(function($item){
            return collect([
                "orderName" => $item->NAIMENOVANIE
            ]);
        });
    } 
    private function getMyWareHouse($request){
    
    }
    private function getMyProduct($request){

    }
}
