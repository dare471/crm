<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $survey = Survey::all();
        return response($survey);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
        $survey = new Survey();
        $survey-> 
        
        $survey->created_at = $request->created_at;
        $survey->save();
        return Response([
            'status' => 'успешный', 
            'statuscode' => 201,
            'data' => 'Успешно создано!' 
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showID($id)
    {
        $survey = Survey::find($id);
        if (is_null($survey)) {
            return response()->json(['error' => true, 'message' => 'Данная запись осутствует'], 404);
        } 
        return Response($survey);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $survey = Tasks::find($id);
        if (is_null($survey)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
            $survey->
            
            $survey->updated_at=$request->updated_at;
            $survey->save();
            return Response([
                'status' => 'успешный',
                'statuscode' => 201,
                'data' => 'Запись добавлена!'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
