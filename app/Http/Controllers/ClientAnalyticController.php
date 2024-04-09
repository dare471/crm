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
use App\Http\Resources\nomenClatureGroup;
use App\Http\Resources\contactInf;
use App\Http\Resources\subcidesAll;
use App\Http\Resources\getMainInfCli;
use App\Http\Resources\getSuppMngr;
use App\Http\Resources\getBusinessPoint;
use App\Http\Resources\GetContractAnalysis;
use App\Http\Resources\GetCropRotation;
use App\Http\Resources\getLastContract;


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
                return  purchaseHistory::collection($query)->all();
        }
        if($request->type == "getAreaStructure"){
                $query = DB::table("CRM_SHYMBULAK_PIVOT_AREA_STRUCTURE as cspa")
                ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspa.OWNER_IIN_BIN")
                ->where("cci.ID", $request->clientId)
                ->orderByDesc("YEAR")
                ->get();
                return  areaStructure::collection($query)->all();
        }
        if($request->type == "getPotentialCult"){
                $query = DB::table("CRM_SHYMBULAK_PIVOT_POTENTIAL_OF_CULTURE as cspp")
                ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspp.IIN_BIN")
                ->where("cci.ID", $request->clientId)
                ->get();
                return  potentialCult::collection($query)->all();
        }
        if($request->type == "getPurchaseHistoryMarket"){
                $query = DB::table("CRM_SHYMBULAK_PIVOT_PURCHASE_HISTORY_BY_MARKET as cspphm")
                ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspphm.CORRECT_APPLICANT_IIN_BIN")
                ->where("cci.ID", $request->clientId)
                ->orderByDesc("YEAR")
                ->get();
                return purchaseHistoryMarket::collection($query)->all();
        }
            if($request->type == "getPivotYieldStructure"){
                $query = DB::table("CRM_SHYMBULAK_PIVOT_YIELD_STRUCTURE as cspys")
                ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspys.OWNER_IIN_BIN")
                ->where("cci.ID", $request->clientId)
                ->orderByDesc("YEAR")
                ->get();
                return  pivotYieldStructure::collection($query)->all();
            }
            if($request->type == "getPivotSubsideRegion"){
                $query = DB::table("CRM_SHYMBULAK_SUBSIDIES_PIVOT as cssp")
                ->where("cssp.CATO", "like", "".$request->regionId."%")
                ->orderByDesc("SEASON")
                ->get();
                return pivotSubsideRegion::collection($query)->all();
            }
            if($request->type == "getPivotSubsideCountry"){
                $query = DB::table("CRM_SHYMBULAK_SUBSIDIES_PIVOT_KZ")
                ->orderByDesc("SEASON")
                ->get();
                return pivotSubsideCountry::collection($query)->all();
            }
   }
    
    public function clientInformation(Request $request){
        switch ($request->type) {
            case "client":
                switch ($request->action) {
                    case "getContract":
                        return $this->getContract($request);
                        break;
                    case "getContact":
                        return $this->getContact($request);
                        break;
                    case "updateContact":
                        return $this->updateContactClient($request);
                        break;
                        case "setContacts":
                            return $this->setContacts($request);
                            break;
                    // case "setContactClient":
                    //     return $this->setContactClient($request);
                    //     break;
                    case "getSubscidesList":
                        return $this->getSubscidesList($request);
                        break;
                    case "getMainInf":
                        $getMainInf = $this->getMainInf($request);
                        $response = response()->json((new getMainInfCli($getMainInf))->toArray($request));
                        return $response;   
                        break;
                    case "getLastContract":
                        $lastContract = $this->getLastContract($request);
                        $response = response()->json((new getLastContract($lastContract))->toArray($request));
                        return $response;                        
                         break;
                    case "getLinkMngr":
                        $getLinkMngr = $this->getLinkMngr($request);
                        $response = response()->json((new getSuppMngr($getLinkMngr))->toArray($request));
                        return $response;     
                        break;
                    case "getBusinessPoint": 
                        return $this->getBusinessPoint($request);
                        break;
                    case "getCropRotation":
                        return $this->getCropRotation($request);
                        break;
                    
                    case "setMainInf":
                        return $this->setMainInf($request);
                        break;
                    case "getContractAnalysis":
                        return $this->getContractAnalysis($request);
                        break;
                    case "getFile": 
                        return $this->getFile($request);
                        break;
                }
                break;
        }
    }
    //get 
    private function getMainInf(Request $request){
        $query = DB::table("CRM_CLIENT_INFO as cci")
        ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.ID", "cci.CLIENT_ID")
        ->select("cci.ID", "cci.ADDRESS", "cci.CLIENT_ID", "NAME", DB::raw("CASE WHEN ccig.GUID IS NULL THEN 0 WHEN ccig.GUID IS NOT NULL THEN 1 END as guid"), "IIN_BIN", "CATO", "DEYATELNOST", "REGION", "DISTRICT")
        ->where("cci.ID", $request->clientId)
        ->get();
     return getMainInfCli::collection($query)->first();
    }
    private function getFile(Request $request){
        $query = DB::table("CRM_ as cci")
        ->get();   
    }
    private function getLastContract(Request $request){
        $query = DB::table("CRM_CLIENT_ID_GUID as cig")
        ->select("cd.ID", "cd.NAIMENOVANIE", "cd.DATA_NACHALA_DEYSTVIYA", "cd.DATA_OKONCHANIYA_DEYSTVIYA", "cd.NOMER", "cd.STATUS", "cd.KONTRAGENT", "cd.MENEDZHER AS manager", "cd.SEZON", "cd.ADRES_DOSTAVKI", "cd.SUMMA_KZ_TG")
        ->join("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "cig.GUID")
        ->where("cig.ID", $request->clientId)
        ->get();
        return getLastContract::collection($query)->first();
    }
    private function getContract(Request $request){
        $list = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA as cds")
        ->select("SEZON")
        ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "cds.DOGOVOR_GUID")
        ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
        ->leftJoin("L1.dbo.NOMENKLATURA as ln", "ln.GUID", "cds.NOMENKLATURA_GUID")
        ->where("cci.ID", $request->clientId)
        ->orderByDesc("SEZON")
        ->groupBy("SEZON")
        ->get();
        return nomenClatureGroup::collection($list)->all();
    }
    private function getLinkMngr(Request $request){
        $subQuery = DB::table("CRM_CLIENT_ID_GUID as ccig")
            ->select("cu.ID", "cu.NAIMENOVANIE", "cu.DIREKTSYA", "cu.DOLZHNOST", "SEZON")
            ->leftJoin("CRM_DOGOVOR as cd", "cd.KONTRAGENT_GUID", "ccig.GUID")
            ->leftJoin("CRM_USERS as cu", "cu.GUID", "cd.MENEDZHER_GUID")
            ->where("ccig.ID", $request->clientId)
            ->groupBy("cu.ID", "cu.NAIMENOVANIE", "cu.DIREKTSYA", "cu.DOLZHNOST", "SEZON")
            ->orderByDesc("SEZON")
            ->get();
         return getSuppMngr::collection($subQuery)->first();
    }
    private function getBusinessPoint(Request $request){
        $query = DB::table("CRM_CLIENT_BUSINESS_PLACE as ccbp")
            ->select("ccbp.ID", "ccbp.CLIENT_ID", "ccbp.NAME", "ccbp.COORDINATE", "csbp.NAME as NAME_C")
            ->leftJoin("CRM_SPR_BUSINESS_PLACE as csbp", "csbp.ID", "ccbp.PLACE_ID")
            ->where("CLIENT_ID", $request->clientId)
            ->get();
         return getBusinessPoint::collection($query)->all();
    }
    private function getContact(Request $request){
        $contact = DB::table("CRM_CLIENT_CONTACTS")
        ->where("CLIENT_ID", $request->clientId)
        ->get();
        return contactInf::collection($contact)->all();
    }
    private function getContractAnalysis(Request $request){
        $query = DB::table("L1.dbo.ANALIZ_DOGOVORA as ad")
        ->select("cd.SEZON", "cci.ID")
        ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "ad.DOGOVOR_GUID")
        ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
        ->where("cci.ID", $request->clientId)
        ->where("cd.STATUS", "Действует")
        ->groupBy("cd.SEZON","cci.ID")
        ->get();
        return GetContractAnalysis::collection($query)->all();
    }
    
    private function getSubscidesList(Request $request){
        $bin = DB::table("CRM_CLIENT_INFO")
        ->where("ID", $request->clientId)
        ->value("IIN_BIN");
        $list = DB::table("CRM_SHYMBULAK_SUBSIDIES")
        ->select("YEAR", "CORRECT_APPLICANT_IIN_BIN")
        ->where("CORRECT_APPLICANT_IIN_BIN", $bin)
        ->orderByDesc("YEAR")
        ->groupBy("YEAR", "CORRECT_APPLICANT_IIN_BIN")
        ->get();
        return subcidesAll::collection($list)->all();
    }
    private function getCropRotation(Request $request){
        $query = DB::table("CRM_SHYMBULAK_PIVOT_AREA_STRUCTURE as cspas")
        ->select("cspas.YEAR")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspas.OWNER_IIN_BIN")
        ->where("cci.ID", $request->clientId)
        ->groupBy("cspas.YEAR")
        ->orderByDesc("cspas.YEAR")
        ->get();
       return getCropRotation::collection($query)->all();
    }
    //set 
    private function setContacts(Request $request){
        $query = DB::table("CRM_CLIENT_CONTACTS")
            ->where("CLIENT_ID", $request->clientId)
            ->where("ID", $request->contactId);
         if ($request->position) {
            $query->update([
               "POSITION" => $request->position,
            ]);
         }
         if ($request->name) {
            $query->update([
               "NAME" => $request->name
            ]);
         }
         if ($request->phoneNumber) {
            $query->update([
               "PHONE_NUMBER" => $request->phoneNumber
            ]);
         }
         if ($request->email) {
            $query->update([
               "EMAIL" => $request->email
            ]);
         }
         return response()->json([
            "message" => "Records update",
            "status" => true
         ]);; 
    }
    private function setMainInf(Request $request){
        $query = DB::table("CRM_CLIENT_INFO")
            ->where("ID", $request->clientId)
            ->update([
               "ADDRESS" => $request->address
            ]);
         return response()->json([
            "message" => "Record update",
            "status" => true
         ]);
    }
    private function setContactClient(Request $request){
        $query = DB::table("CRM_CLIENT_CONTACTS")
        ->insert([
           "POSITION" => $request->position,
           "CLIENT_ID" => $request->clientId,
           "NAME" => $request->name, 
           "PHONE_NUMBER" => $request->phNumber,
           "EMAIL" => $request->email,
           "AUTHOR_ID" => $request->userId,
           "MAIN_CONTACT" => $request->mainC,
           "DESCRIPTION" => $request->description
        ]);
        return response()->json([
           "status" => true,
           "message" => "sucess"
        ]);
     }
    private function updateContactClient(Request $request){
        $query = DB::table("CRM_CLIENT_CONTACTS")
        ->where("ID", $request->id)
        ->update([
           "POSITION" => $request->position,
           "NAME" => $request->name,
           "PHONE_NUMBER" => $request->phNumber,
           "EMAIL" => $request->email,
           "AUTHOR_ID" => $request->authorId,
           "ACTUAL" => $request->mainContact,
           "DESCRIPTION" => $request->description,
           "MAIN_CONTACT" => $request->mainContact
        ]);
        return collect(["message" => "data is updated", "status" => 201]);
     }
}