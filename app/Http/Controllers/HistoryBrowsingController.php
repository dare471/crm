<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Resources\HistoryBrowsingResource;
class HistoryBrowsingController extends Controller
{
    public function HistoryBrowsing(Request $request){
        if($request->type == "listBrowsing"){
            $query = DB::table("CRM_HISTORY_BROWSING")
            ->select(
                "ID", 
                "USER_ID", 
                "REGION", 
                "MODE", 
                "DISTRICT", 
                "CLIENT_FIELDS"
                )
            ->where("USER_ID", $request->userID)
            ->get();
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => HistoryBrowsingResource::collection($query)
            ]);
        }
        if($request->type == "createBrowsing"){
            $query = DB::table("CRM_HISTORY_BROWSING")
            ->insert([
                'USER_ID' => $request->userID,
                'REGION' => $request->region,
                'MODE' => $request->mode,
                'DISTRICT' => $request->district,
                'CLIENT_FIELDS' => $request->clientFields
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
