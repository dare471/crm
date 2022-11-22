<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PowerBiController extends Controller
{
    public function PowerBiReport(Request $request)
    {
        if($request->type == "allReport"){
            $rep = DB::table('CRM_REPORT_LIST_AA')
            ->select('*')
            ->get();
            return response([
                'status' => 'Success',
                'status' => 201,
                'data' => $rep
            ]);
        }
        if($request->type == "insertReport"){
            $rep = DB::table('CRM_REPORT_LIST_AA')
            ->insert([
                'NAME' => $request->name,
                'LINK' => $request->powerbiLink,
                'GROUP' => $request->group
            ]);

            return response([
                'status' => 'Success',
                'status' => 201,
                'data' => $request->name
            ]);
        }
        if($request->type == "updateReport"){
            $rep = DB::table('CRM_REPORT_LIST_AA')
            ->where('id', $request->id)
            ->update([
                'NAME' => $request->name,
                'LINK' => $request->powerbi_link,
                'GROUP' => $request->group
            ]);
            return response([
                'status' => 'Success',
                'status' => 201,
                'data' => $request->name
            ]);
        }
        if($request->type == "deleteReport"){
            $rep = DB::table('CRM_REPORT_LIST_AA')
            ->where('id', $request->id)
            ->delete();
            return response([
                'success' => true,
                'status' => 201,
                'data' => 'that"s id deleted'
            ]);
        }
        else{
            return response()->json([
                "status" => 201,
                "message" => "Send correct type"
            ]);
        }
    }
}