<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Today;
use Illuminate\Support\Facades\Auth;
use Validator;

class TodayController extends Controller
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
        $today = Today::all();
        return Response($today);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $rules = [
            'title' => 'required|min:3',
            'taskId' => 'required|min:1',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $today = new Today();
        //$today->id = $request->id;
        $today->title = $request->title;
        $today->taskId = $request->taskId;
        $today->created_at = date("Y-m-d")."T".date("H:i:s")."Z";
        $today->updated_at = date("Y-m-d")."T".date("H:i:s")."Z";
        $today->save();
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
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($taskId)
    {
        //
        $today = Today::where('taskId', $taskId);
        $today->delete();
        return Response([
            'status' => 'успешный',
            'statuscode' => 202,
            'data' => 'Успешно удалено'
        ]);
    }
    protected function guard() {
        return Auth::guard();
    }
}
