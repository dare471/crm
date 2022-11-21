<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listc;
use Illuminate\Support\Facades\Auth;
use Validator;

class DocumentCController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'name_t' => 'required|min:3',
            'iin_t' => 'required|min:3',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $document_c = new Listc();
        //$document_c->id = $request->id;
        $document_c->user_id = $request->user_id;
        $document_c->name_t = $request->name_t;
        $document_c->iin_t = $request->iin_t;
        $document_c->save();
        return Response([
            'status' => 'успешный',
            'statuscode' => 201,
            'data' => 'Успешно создано!'
        ]);
    }

    public function created_j(Request $request)
    {
        DB::connection('CRM_DWH')
                ->table('CRM_JOURNAL')
                ->insert(
                ['NAME' =>  $request->name, 'DETAIL_TEXT' => $request->detail_text, 'DESCRIPTION' => $request->description, 'CREATED_TIME' => Carbon::now()->toDateTimeString(), 'UPDATE_TIME' => Carbon::now()->toDateTimeString(), 'CREATED_BY' => $request->created_by, 'CATEGORY' => $request->category, 'PROPERTIES_JSON' => $request->json_b]
                );
    }

    public function list_j(Request $request){
        $list = DB::connection('CRM_DWH')
                ->table('CRM_JOURNAL')
                ->select('*')
                ->where('CREATED_BY', $request->login)
                    ->orwhere('ID', $request->id)
                ->get();
            return response([
                'status' => 'success',
                'statuscode' => 201,
                'data' => $list
            ]);
    }

    public function list_category(){
        $list = DB::connection('CRM_DWH')
                ->table('CRM_JOURNAL')
                ->select('*')
                ->get();
                return response([
                    'status' => 'success',
                    'statuscode' => 201,
                    'data' => $list
                ]);
    } 
}
