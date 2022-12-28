<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseClusterController;

class ContractController extends Controller
{
    //Маршрут функции
    public function Contracts(Request $request)
    {
        if($request->type == "managerContracts"){
            return  ContractController::AllContracts($request);
        }
        if($request->type == "detailContract"){
            return ContractController::DetailContracts($request);
        }
    }

    //Вывод всех договоров определнного Менеджера
    public function AllContracts($request){
        $query = DB::table("CRM_CLIENT_ID_GUID as CCIG")
            ->leftjoin("CRM_DOGOVOR as CD", "CD.KONTRAGENT_GUID", "CCIG.GUID")
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.CLIENT_ID", "CCIG.ID")
            ->select(
                DB::raw("'managerContracts' as type"),
                "CU.ID as managerID", 
                "CU.NAIMENOVANIE as managerName", 
                "CU.DIREKTSYA as direction",
                "CU.DOLZHNOST as managerTitle",
                "CCIG.ID as clientID",
                "CD.KONTRAGENT as contracteClient",
                "CD.ID as contractsId",
                "CD.NAIMENOVANIE as contractName",
                "CCI.IIN_BIN as clientIin",
                "SEZON as season",
                "CD.USLOVIYA_OPLATY as termsOfPayment",
                "CD.SPOSOB_DOSTAVKI as deliveryMethod",
                "CD.ADRES_DOSTAVKI as deliveryAddress",
                "CD.SUMMA_KZ_TG as sumContracts"
                )
            ->where("CU.ID", $request->managerId)
            ->where("CD.OSNOVNOY_DOGOVOR", "")
            ->whereIn("SEZON", ["Сезон 2022"])
            ->limit(50)
            ->orderByDesc("CD.NAIMENOVANIE")
            ->get();
            return (new ResponseClusterController)->ResponseFunction($query, null);

    }
    
    //Отобразить детально договор
    public function DetailContracts($request){
            $bodyContract = DB::table("CRM_CLIENT_ID_GUID as CCIG")
                ->leftjoin("CRM_DOGOVOR as CD", "CD.KONTRAGENT_GUID", "CCIG.GUID")
                ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
                ->select(
                    DB::raw("'detailContract' as type"),
                    "CD.ID as contractId", DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) as contractsGuid"),"CU.ID as managerID",
                    "CU.NAIMENOVANIE as managerName",
                    "CU.DIREKTSYA as direction",
                    "CU.DOLZHNOST as managerTitle",
                    "CCIG.ID as KONTRAGENT_ID",
                    "CD.KONTRAGENT as clientName",
                    "CD.NAIMENOVANIE as contractName",
                    "SEZON as contractSeason",
                    "CD.USLOVIYA_OPLATY as termOfPayment",
                    "CD.SPOSOB_DOSTAVKI as deliveryMethod",
                    "CD.ADRES_DOSTAVKI as deliveryAddress",
                    "CD.SUMMA_KZ_TG as contractSum",
                    "CD.NOMER_DOP_SOGLASHENIYA as contractAddicional"
                )
            ->where("CD.ID", $request->contractId)
            ->get();

            $specificateContract = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA AS CDS")
                ->leftjoin("CRM_DOGOVOR AS CD", "CD.GUID", "CDS.DOGOVOR_GUID")
                ->select(
                    "CD.NAIMENOVANIE as contractName",
                    "PERIOD as periodContract",
                    "NOMENKLATURA as productName",
                    "VIDY_KULTUR as typeCulture",
                    "KOLICHESTVO as productCount",
                    "TSENA AS priceProduct",
                    "TSENA_SO_SKIDKOY as priceDiscount",
                    "TSENA_PO_PRAYS_LISTU as priceCatalog",
                    "TSENA_MIN as priceMin",
                    "SUMMA as sumProductWithContract",
                    "SUMMA_SO_SKIDKOY as sumDiscount",
                    "SKLAD_OTGRUZKI as warehouse",
                    "CDS.SUMMA_KZ_TG as sumProduct"
                )
            ->where("CD.ID", $request->contractId)
            ->get();
            return (new ResponseClusterController)->ResponseFunction($bodyContract, $specificateContract); 
    }
}
