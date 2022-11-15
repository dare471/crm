<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upcoming;
use Illuminate\Support\Facades\Auth;
use Validator;

class UpcomingController extends Controller
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
        $upcoming = Upcoming::all();
        return Response($upcoming);
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
            'waiting' => 'required|boolean',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $upcoming = new Upcoming();
        $upcoming->title = $request->title;
        $upcoming->taskId = $request->taskId;
        $upcoming->waiting = $request->waiting;
        $upcoming->created_at = date("Y-m-d")."T".date("H:i:s")."Z";
        $upcoming->updated_at = date("Y-m-d")."T".date("H:i:s")."Z";
        $upcoming->save();
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
        $upcoming = Upcoming::where('taskId', $taskId);
        if (is_null($upcoming)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
        $upcoming->delete();
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
