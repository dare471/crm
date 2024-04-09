<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PlannedMeetingMobileList;
use App\Http\Resources\PlannedMettingDetail;
use App\Jobs\ProcessSerializeForVisitAnswer;
use Illuminate\Support\Carbon;

class PlannedMeetingForMobileController extends Controller
{
    public function MainRouteCondition(Request $request){
        $type = $request->input('type');
        $action = $request->input('action');
        switch ($type) {
            case 'plannedMeeting':
                switch ($action) {
                    case 'getMeetings':
                        return $this->getMeeting($request);
                    case 'setMeeting':
                        return $this->setMeeting($request);
                    case 'updateMeeting':
                        return $this->updateMeeting($request);
                    case 'storeMeetingResult':
                        return $this->storeMeetingResult($request);
                    case 'getMeetingDetail': 
                        return $this->getMeetingDetail($request);
                    case 'setStartVisit':
                        return $this->setStartVisit($request);
                    case 'setFinishVisit':
                        return $this->setFinishVisit($request);
                    case "setCoordinateVisit":
                        return $this->setCoordinateVisit($request);
                    case "fixedSurvey": 
                        return $this->fixedSurvey($request);
                    default:
                        return response()->json(['message' => 'Invalid action'], 400);
                }
                break;
            case 'meetingSurvey':
                switch($action){
                    case "fixedSurvey": 
                        return $this->fixedSurvey($request); 
                    default:
                        return response()->jsom(['message' => 'invalid action']);
                }
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
    }
    private function setCoordinateVisit(Request $request){
        $query = DB::table("CRM_VISIT_TO_COORDINATE")
            ->insertGetId([
                "visitId" => $request->visitId,
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "coordinate" => (string)$request->longitude . ", " . $request->latitude,
                "kadastrNumber" => ''
            ]);
            return collect(['message' => true]);
    }
    private function setMeeting($request) {
        try {
            
            DB::transaction(function () use ($request) {
                $dateToVisit = $request->input('dateToVisit');
                $visitData = [
                    "USER_ID" => $request->input('userId'),
                    "CLIENT_ID" => $request->input('clientId'),
                    "DATE_TO_VISIT" =>DB::raw("CONVERT(datetime, '{$dateToVisit}', 121)"),
                    "SOURCE" => 0,
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
    private function getMeeting($request){
        $query = DB::table("CRM_VISIT_TO_DATE as cdtv")
        ->select("cdtv.DATE_TO_VISIT", "cdtv.ID", "CLIENT_ID", "css.NAME", "cdtv.STATUS", "cdtv.SOURCE", "cdtv.TARGET_DESCRIPTION")
        ->leftJoin("CRM_SPR_STATUS as css", "css.ID", "cdtv.STATUS")
        ->where("USER_ID", $request->userId)
        ->orderByDesc("ID")
        ->get();
      return PlannedMeetingMobileList::collection($query)->all();
    }
    private function getMeetingDetail($request){
      $queryClient = DB::table("CRM_VISIT_TO_DATE as cvtd")
      ->select("cvtd.ID as visitId", "cvtd.USER_ID as userId", "cvtd.CREATE_DATE as createDate", 
      "cvtd.SOURCE as source", 
      "cvtd.STATUS as statusVisit", 
      "cci.NAME as clientName",
      "cci.buisnessCategory",
      "cci.ADDRESS", 
      "cci.IIN_BIN",
      "cci.managerId",
      "cvtd.UPDATED as updated", 
      "cvtd.DATE_TO_VISIT as dateToVisit",
      "cvtd.FINISH_TO_VISIT as finishToVisit",
      "cvtd.CLIENT_ID as clientId", 
      "cvtd.TARGET_DESCRIPTION as targetDescription",
      "cvtdp.ID as propertiesId",
      "cvtdp.TYPE_VISIT_ID as visitTypeId",
      "cvtdp.MEETING_COORDINATE",
      "cvtdp.PLOT",
      "cvtdp.DURATION",
      "cvtdp.DISTANCE",
      "cvtdp.TYPE_MEETING as meetingTypeId",
      "cvtdp.STARTVISIT as startVisit",
      "cvtdp.FINISHVISIT as finishVisit",
      "cvtdp.DESCRIPTION as descriptionVisit",
      "cvtdp.PLACE_DESCRIPTION as placeDescription")
      ->leftJoin("CRM_VISIT_TO_DATE_PROPERTIES as cvtdp", "cvtd.ID", "cvtdp.VISIT_ID")
      ->leftJoin("CRM_CLIENT_INFO as cci", "cci.ID", "cvtd.CLIENT_ID")
      ->where("cvtd.ID", $request->visitId)
      ->get();
    
      return PlannedMettingDetail::collection($queryClient)
         ->map(function ($item) {
            return $item;
         })->first();
    }
    private function updateMeeting($request){
        $query = DB::table("CRM_VISIT_TO_DATE")
        ->where("ID", $request->visitId)
        ->updateOrInsert([
           "USER_ID" => $request->userId,
           "CLIENT_ID" => json_encode($request->properties),
           "DATE_TO_VISIT" => $request->dateToVisit,
           "SOURCE" => 0
        ]);
        $arr = $request->properties;
        $i = 0;
        foreach ($arr as $a) {
           $p = json_encode($a, true);
           $coord=json_decode($p)->coordinate;
           $query2 = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
              ->update([
                 "VISIT_ID" => $query,
                 "CLIENT_ID" => json_decode($p)->clientId,
                 "TYPE_VISIT_ID" => json_decode($p)->typeVisit,
                 "TYPE_MEETING" => json_decode($p)->placeMeeting,
                 "MEETING_COORDINATE" => json_encode($coord),
                 "PLOT" => json_decode($p)->plotId,
                 "DURATION" => json_decode($p)->duration,
                 "DISTANCE" => json_decode($p)->distance,
                 "STATUSVISIT" => 0
              ]);
        }
        return response()->json([
           "message" => "Meeting to save"
        ]);
    }
    private function setStartVisit($request){
        $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
        ->where("VISIT_ID", $request->visitId)
        ->update([
           "STARTVISIT" => DB::raw("GETDATE()")
        ]);
        
        return response()->json([
           "message" => "finishdate Fixed"
        ]);
    }
    private function setFinishVisit($request){
        $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
         ->where("VISIT_ID", $request->visitId)
         ->update([
            "FINISHVISIT" => DB::raw("GETDATE()")
         ]);
         return response()->json([
            "message" => "startdate Fixed"
         ]);
    }    
    private function storeMeetingResult($request){
        DB::table('CRM_VISIT_TO_DATE_PROPERTIES')
            ->where('VISIT_ID', $request->visitId)
            ->update([
                'RESULT_JSON' => json_encode($request->result)
            ]);

        DB::table('CRM_VISIT_TO_DATE')
            ->where('ID', $request->visitId)
            ->update(['UPDATED' => DB::raw('GETDATE()'), 'STATUS' => 1]);
        
        return response()->json([
            'status' => true,
            'message' => 'Set result successful'
        ]);
    }
    private function fixedSurvey($request){
        $query = DB::table("CRM_SURVEY_MEETING")
            ->insert([
               "visitId" => $request->visitId,
               "fieldInspectionId" => json_encode($request->fieldInspection),
               "recomendationId" => json_encode($request->recomendation),
               "contractComplicationsId" => json_encode($request->contractComplication),
               "workdoneId" => json_encode($request->workDone),
               "fileVisit" => json_encode($request->fileVisit)
            ]);
            
            $this->serializeAnswerVisit($request);
            return response()->json([
                'success' => true,
                "recomendationId" => json_encode($request->recomendation),
                "recomendation" => $request->recomendation,
            ]);
    }
    private function serializeAnswerVisit($data){
      
        $json = collect([
            "workDone" => $this->workDone($data->workDone),
            "fieldInspection" => $this->fieldInspection($data->fieldInspection),
            "difficulties" => $this->difficulties($data->contractComplication),
            "recomendation" => $this->recomendation($data->recomendation)
        ]);
        $query = DB::table("CRM_VISIT_TO_DATE_PROPERTIES")
        ->where("VISIT_ID", $data->visitId)
        ->update([
            "RESULT_JSON" => json_encode($json, JSON_UNESCAPED_UNICODE)
        ]);    
      
    }
    private function workDone($data){
        $query = DB::table("CRM_SPR_WORK_DONE")
        ->select("id","name")
        ->whereIn("ID",$data)
        ->get();
        return $query;
    } 
    private function fieldInspection($data) {
        $dataCollection = collect($data);
    
        // Группировка данных по categoryId
        $groupedData = $dataCollection->groupBy('categoryId');
    
        // Получение данных для culture
        $cultureIds = $groupedData->get(1, collect())->pluck('id')->toArray();
        $cultureData = DB::table('CRM_VISIT_SPR_CULTURE')
            ->select("id", "name")
            ->whereIn("id", $cultureIds)
            ->first();
    
        $result = [
            'culture' => $cultureData
        ];
    
        // Отображение для stagesViewCulture и idetificationProblem
        $keysMapping = [
            2 => 'stagesViewCulture',
            3 => 'idetificationProblem'
        ];
    
        $groupedData->except(1)->map(function($items, $categoryId) use (&$result, $keysMapping) {
            $ids = $items->pluck('id')->toArray();
            $table = '';
    
            switch ($categoryId) {
                case 2:
                    $table = 'CRM_VISIT_SPR_STAGES_VIEW_CULTURE';
                    break;
                case 3:
                    $table = 'CRM_VISIT_SPR_IDENTIFICATION_PROBLEM';
                    break;
            }
    
            if ($table) {
                $query = DB::table($table)
                    ->select("id", "name")
                    ->whereIn("id", $ids)
                    ->get();
    
                $result[$keysMapping[$categoryId]] = $query->toArray();
            }
        });
    
        return [$result];
    }
    private function difficulties($data){
        $query = DB::table("CRM_SPR_CONTRACT_COMPLICATIONS")
        ->select("id", "name")
        ->whereIn("id", $data)
        ->get();
        return collect(["hardStages" => $query, "description" => ""]);
    }
    private function recomendation($data) {
        $query = DB::table("CRM_SPR_RECOMENDATIONS_MEETING")
            ->whereIn("ID", $data)
            ->get();
    
        // Используем reduce вместо map, чтобы создать единый массив
        $result = $query->reduce(function ($carry, $item) {
            switch ($item->id) {
                case 1:
                    $carry["visit"] = [
                        "name" => $item->name,
                        "answer" => 1,
                        "description" => ""
                    ];
                    break;
                case 2:
                    $carry["product"] = [
                        "name" => $item->name,
                        "answer" => 1,
                        "description" => ""
                    ];
                    break;
                case 3:
                    $carry["arrangement"] = [
                        "name" => $item->name,
                        "answer" => 1,
                        "description" => ""
                    ];
                    break;
            }
            return $carry;
        }, []);
    
        return $result;
    }    
}
