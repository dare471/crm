<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Resources\HistoryBrowsingResource;
class HistoryBrowsingController extends Controller
{
    public function HistoryBrowsing(Request $request){
        if($request->type == "listBrowsing"){
            $query = DB::table("CRM_HISTORY_BROWSING as chb")
            ->leftJoin("CRM_CLIENT_INFO as cci", "cci.ID", "chb.CLIENT_ID")
            ->leftJoin("CRM_CLIENT_PROPERTIES_4326 as ccp", "ccp.ID", "chb.CLIENT_FIELDS")
            ->select(
                "chb.ID", 
                "chb.USER_ID", 
                "chb.REGION",
                "cci.ADDRESS",
                "cci.ID as CID",
                "cci.NAME", 
                "chb.CLIENT_FIELDS",
                "ccp.FIELDS"
                )
            ->where("USER_ID", $request->userId)
            ->get();
            return HistoryBrowsingResource::collection($query)->all();
        }
        if($request->type == "createBrowsing"){
            $query = DB::table("CRM_HISTORY_BROWSING")
            ->insert([
                'USER_ID' => $request->userId,
                'REGION' => $request->regionId,
                'DISTRICT' => $request->districtId,
                'CLIENT_ID' => $request->clientId,
                'CLIENT_FIELDS' => $request->clientPlotId
            ]);
            return response()->json([
                'succes' => true,
                'status' => 201,
                'message' => 'Record created'
            ]);
        }
        if($request->type == "deleteBrowsing"){
            $query = DB::table("CRM_HISTORY_BROWSING")
            ->where("ID", $request->idRecord)
            ->delete();
            return response()->json([
                'succes' => true,
                'status' => 201,
                'message' => 'Record delete'
            ]);
        }
    }
}
