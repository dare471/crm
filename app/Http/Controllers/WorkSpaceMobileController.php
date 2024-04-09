<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\sprTypeVisit;

class WorkSpaceMobileController extends Controller
{
    public function MainRouteCondition(Request $request)
    {
        $type = $request->input('type');
        $action = $request->input('action');

        switch ($type) {
            case 'handBook':
                switch ($action) {
                    case 'getHandbook':
                        return $this->getHandbook($request);
                    default:
                        return response()->json(['message' => 'Invalid action'], 400);
                }
                break;
            case 'contactVisit':
                switch($action){
                    case 'setContact':
                        return $this->setContact($request);
                    default:
                        return response()->json(['message' => 'Invalid action'], 400);
                }
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
    }
    private function setContact(Request $request)
    {
        $target = $request->input('target');
        switch($target){
            case 'addContact':
                return $this->fetchData_Visit('CRM_VISIT_CONTACTS', $request);
            default:
                return response()->json(['message' => 'Invalid target'], 400);
        }
    }
    private function getHandbook(Request $request)
    {
        $target = $request->input('target');

        switch ($target) {
            case 'sprTypeVisit':
                return $this->fetchData('CRM_SPR_TYPE_VISIT');
            case 'sprTypeMeeting':
                return $this->fetchData('CRM_SPR_TYPE_MEETING');
            case 'sprTypeTransport':
                return $this->fetchData('CRM_SPR_TRANSPORT');
            case 'sprTypeStatus':
                return $this->fetchData('CRM_SPR_STATUS');
            case 'sprTypeWorkDone':
                return $this->fetchData('CRM_SPR_WORK_DONE');
            case 'sprTypeRecomendationMeeting':
                return $this->fetchData('CRM_SPR_RECOMENDATIONS_MEETING');
            case 'sprTypeFieldInspection':
                return $this->fetchData('CRM_SPR_FOR_FIELD_INSPECTION');
            case 'sprTypeCulture':
                return $this->fetchData('CRM_SPR_CULTURE');
            default:
                return response()->json(['message' => 'Invalid target'], 400);
        }
    }
    public function fetchData_Visit($tableName, $request)
    {
        $data = DB::table($tableName)
        ->insert([
            'VISIT_ID' => $request->visitId,
            'CLIENT_ID' => $request->clientId,
            'CONTACT_ID' => $request->contactId,
            'USER_ID' => $request->userId
        ]);
    }
    private function fetchData($tableName)
    {
        $data = DB::table($tableName)->get();
        return sprTypeVisit::collection($data, 200)->all();
    }
}
