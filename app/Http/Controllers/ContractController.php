<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function Contracts(Request $request)
    {
        if($request->type == "allContracts"){
            $query = DB::table("CRM_CLIENT_ID_GUID as CCIG")
            ->leftjoin("CRM_DOGOVOR as CD", "CD.KONTRAGENT_GUID", "CCIG.GUID")
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->leftjoin("CRM_CLIENT_INFO as CCI", "CCI.CLIENT_ID", "CCIG.ID")
            ->select("CU.ID as managerID", 
                "CU.NAIMENOVANIE as managerName", 
                "CU.DIREKTSYA as direction",
                "CU.DOLZHNOST as managerTitle",
                "CCIG.ID as clientID",
                "CD.KONTRAGENT as contracteClient",
                DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) as contractsGuid"),
                "CD.NAIMENOVANIE as contractName",
                "CCI.IIN_BIN as clientIin",
                "SEZON as season",
                "CD.USLOVIYA_OPLATY as termsOfPayment",
                "CD.SPOSOB_DOSTAVKI as deliveryMethod",
                "CD.ADRES_DOSTAVKI as deliveryAddress",
                "CD.SUMMA_KZ_TG as sumContracts"
                )
            ->where("CU.ID", $request->managerID)
            ->where("CD.OSNOVNOY_DOGOVOR", "")
            ->whereIn("SEZON", ["Сезон 2022"])
            ->limit(50)
            ->orderByDesc("CD.NAIMENOVANIE")
            ->get();
            return response()->json([
                "status" => 201,
                "succes" => true,
                "data" => $query
            ]);
        }
        if($request->type == "detailContracts"){
            $specificate = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA AS CDS")
            ->leftjoin("CRM_DOGOVOR AS CD", "CD.GUID", "CDS.DOGOVOR_GUID")
            ->select("CD.NAIMENOVANIE as contractName",
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
            ->where("CD.GUID", $request->contractsGuid)
            ->get();

            $mainQuery = DB::table("CRM_CLIENT_ID_GUID as CCIG")
            ->leftjoin("CRM_DOGOVOR as CD", "CD.KONTRAGENT_GUID", "CCIG.GUID")
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->select(DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS contractsGuid"),"CU.ID as managerID",
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
            ->where("CD.GUID", $request->contractsGuid)
            ->get();
           
            $additional = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS GUID"),
            "CD.NAIMENOVANIE as additionContractName",
            DB::raw("CONVERT(NVARCHAR(MAX), OSNOVNOY_DOGOVOR, 1) AS OSN"))
            ->where("CD.OSNOVNOY_DOGOVOR", $request->contractsGuid)
            ->get();

            return response()->json([
                "succes" => true,
                "status" => 201,
                "specificationContracts" => $specificate,
                "dataContracts" => $mainQuery,
                "additionalContracts" => $additional
            ]);
        }
        if($request->type == ""){

        }
    }
}
