<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\sprTypeVisit;
use App\Http\Resources\NotesClient;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use App\Http\Resources\FavoritesResource;

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
            case 'notesClient': 
                switch($action){
                    case 'setNote':
                        return $this->fetchNote('CRM_COMMENT_ELEMENT', $request);
                    case 'getNote':
                        return $this->getNote('CRM_COMMENT_ELEMENT', $request);
                    default :
                        return response()->json(['message' => 'invalid target'], 400);
                }
            case 'contactVisit':
                switch($action){
                    case 'addContact':
                        return $this->addContact('CRM_CLIENT_CONTACTS', $request);
                    case 'editContact':
                        return $this->editContact('CRM_CLIENT_CONTACTS', $request);
                    case 'getContact':
                        return $this->getContact('CRM_CLIENT_CONTACTS', $request);
                    default:
                        return response()->json(['message' => 'Invalid target'], 400);
                }
            case 'file': 
                switch($action){
                    case 'get':
                        return $this->getFile($request);
                    case 'set':
                        return $this->insertRecordFile($request);
                }
            case 'favorites':
                switch($action){
                    case 'getClient':
                        return $this->fetchFavorites('CRM_PRIORITY_CLIENTS', $request);
                    default:
                        return response()->json(['message' => 'Invalid target'], 400);
                    }
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
    }

    private function getHandbook(Request $request)
    {
        $target = $request->input('action');

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
                return response()->json(['message' => 'Invalid action'], 400);
        }
    }

    private function getFile(Request $request)
    {
        $data = DB::table('CRM_UPLOADS_FILE')
        ->where('linkElement', $request->visitId)
        ->where('clientId', $request->clientId)
        ->get();

        return $data->all();
    }

    public function uploadFiles($request)
    {     
        $filename = uniqid() . '_' . $request->getClientOriginalName();
        $request->move(public_path('uploads'), $filename);
        $filePath = 'uploads/' . $filename; // Добавление пути к файлу в массив
        return $filePath;
    }

    private function insertRecordFile($request){
        DB::table('CRM_UPLOADS_FILE')
        ->insert([
            "filePath" => $this->uploadFiles($request->file('files')),
            "fileRefers" => $request->fileRefers,
            "author" => $request->createdBy,
            "client" => $request->clientId,
            "filefrom" => $request->fileFrom,
            "linkElement" => $request->visitId,
            "type" => 'PHOTO',
            "type_media" => 'PHOTO'
        ]);
        return response()->json([
            "message" => "success"
        ]);
    }

    private function determineFileType($file)
    {
        $mimeType = $file->getMimeType();

        // Список типов для изображений, аудио и документов
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $audioTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/x-m4a', 'audio/m4a'];
        $fileTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword', 'application/vnd.ms-excel'];

        if (in_array($mimeType, $imageTypes)) {
            return 'IMAGE';
        } elseif (in_array($mimeType, $audioTypes)) {
            return 'AUDIO';
        } elseif (in_array($mimeType, $fileTypes)) {
            return 'FILE';
        } else {
            return 'UNKNOWN'; // Лучше использовать "UNKNOWN" для всех других типов файлов
        }
    }

    private function getNote($tableName, $request){
        $data = DB::table($tableName)
        ->where('ELEMENT_ID', $request->clientId)
        ->where('CATEGORY_CHAPTER_ID', $request->categoryChapterId)
        ->get();
        return NotesClient::collection($data)->all();
    }

    private function fetchNote($tableName, $request){
        $data = DB::table($tableName)
        ->insertGetId([
            'ELEMENT_ID' => $request->clientId,
            'DESCRIPTION' => $request->description,
            'CREATED_BY' => $request->userId,
            'CATEGORY_CHAPTER_ID' => $request->categoryChapterId, //7
            'TYPE' => $request->typeComment, //1 
        ]);

        return response()->json([
            "message" => "notes add succes saved", 
            "data" => $data
        ]);
    }

    private function getContact($tableName, $request){
        $query = DB::table($tableName)
        ->where('CLIENT_ID', $request->clientId)
        ->get();
        return $query;
    }

    private function addContact($tableName, $request){
            $query = DB::table($tableName)
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

    private function editContact($tableName, $request){
            $query = DB::table($tableName)
            ->where("ID", $request->id)
            ->update([
                "POSITION" => $request->position,
                "NAME" => $request->name, 
                "PHONE_NUMBER" => $request->phNumber,
                "EMAIL" => $request->email,
                "MAIN_CONTACT" => $request->mainC,
                "DESCRIPTION" => $request->description
            ]);
            return response()->json([
                "status" => true,
                "message" => "sucess"
            ]);
    }

    private function fetchData($tableName)
    {
        $data = DB::table($tableName)
        ->get();
        return sprTypeVisit::collection($data, 200)->all();
    }

    public function fetchFavorites($tableName, $request) {
        $query = DB::table($tableName)
            ->select("CLIENT_ID as clientId", "CLIENT_NAME as clientName", "BIN as clientBin", "YEAR as year", "SALES_AMOUNT as salesAmount", "MARGIN as margin")
            ->where("MANAGER_ID", $request->userId)
            ->whereNotNull("CLIENT_ID")
            ->get();
    
        $grouped = $query->groupBy('clientId')->map(function ($items) {
            $clientData = $items->first();
    
            $yearlyData = $items->groupBy('year')->map(function ($yearItems) {
                $year = $yearItems->first()->year; // Получение года из первого элемента коллекции
                return [
                    'year' => $year,
                    'salesAmount' => "".number_format($yearItems->sum('salesAmount'), '0', '.', ' ')."₸",
                    'margin' => $yearItems->sum('margin')
                ];
            })->values()->toArray(); // Преобразование в массив
            
            return [
                'clientId' => (int)$clientData->clientId,
                'clientName' => $clientData->clientName,
                'clientBin' => $clientData->clientBin,
                'yearlyData' => $yearlyData
            ];
        })->values(); // Удаление ключей верхнего уровня
    
        return $grouped;
    }

}
