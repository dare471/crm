<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PlannedMeetingMobileList;
use App\Http\Resources\PlannedMettingDetail;
use App\Jobs\ProcessSerializeForVisitAnswer;
use Illuminate\Support\Carbon;

class VisitMController extends Controller
{
    public function createVisit(Request $request){
        try {
            DB::transaction(function () use ($request) {
                $dateToVisit = $request->input('dateToVisit');
                $dateToFinish = $request->input('dateToFinish');
                $visitData = [
                    "USER_ID" => $request->input('userId'),
                    "CLIENT_ID" => $request->input('clientId'),
                    "DATE_TO_VISIT" =>DB::raw("CONVERT(datetime, '{$dateToVisit}', 121)"),
                    "FINISH_TO_VISIT" => DB::raw("CONVERT(datetime, '{$dateToFinish}', 121)"),
                    "SOURCE" => 0,
                    "IS_ALL_DAY" => $request->input('isAllDay'),
                    "TARGET_DESCRIPTION" => $request->input('description')
                ];
    
                $visitId = DB::table("CRM_VISIT_TO_DATE")
                    ->insertGetId($visitData);
                
                $visitPropertiesData = [
                    "VISIT_ID" => $visitId,
                    "TYPE_VISIT_ID" => $request->input('typeVisit'),
                    "TYPE_MEETING" => strtotime($request->input('placeMeeting')), // Непонятно, почему здесь используется strtotime
                    "MEETING_COORDINATE" => '0',
                    "PLOT" => '0',
                    "DURATION" => '0',
                    "DISTANCE" => '0',
                    "STATUSVISIT" => 0
                ];
        
                DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
                    ->insert($visitPropertiesData);
        
                $visitContactsData = [
                    "VISIT_ID" => $visitId,
                    "CLIENT_ID" => $request->input('clientId'),
                    "CONTACT_ID" => $request->input('contactId'),
                    "USER_ID" => $request->input('userId')
                ];
        
                DB::table("CRM_VISIT_CONTACTS")
                    ->insert($visitContactsData);
            });
    
            return response()->json([
                "message" => "Meeting saved"
            ]);
    
        } catch (\Exception $e) {
            // Ловим исключения и возвращаем сообщение об ошибке в ответе
            return response()->json(['error' => $e->getMessage()],  500);
        }
    }
    
    public function deleteVisit(Request $request){

    }
    
    public function upgradeVisit(Request $request){

    }

    public function getVisit(Request $request){
        
    }    
}
