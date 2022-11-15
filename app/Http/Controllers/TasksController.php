<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tasks;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
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
        $tasks = Tasks::all();
        return response()->json($tasks->toArray());
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
            'description' => 'required|min:3',
            'status' => 'required|min:3'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $tasks = new Tasks();
        $tasks->title = $request->title;
        $tasks->description = $request->description;
        $tasks->status = $request->status;
        $tasks->created_at = $request->created_at;
        $tasks->save();
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
        $tasks = Tasks::find($id);
        if (is_null($tasks)) {
            return response()->json(['error' => true, 'message' => 'Данная запись осутствует'], 404);
        } 
        return Response($tasks);
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
        $rules = [
            'title' => 'required|min:3',
            'description' => 'required|min:3',
            'status' => 'required|min:3'
        ];
        $validator = validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tasks = Tasks::find($id);
        if (is_null($tasks)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
            $tasks->title = $request->title;
            $tasks->description = $request->description;
            $tasks->status = $request->status;
            $tasks->updated_at=$request->updated_at;
            $tasks->save();
            return Response([
                'status' => 'успешный',
                'statuscode' => 201,
                'data' => 'Запись добавлена!'
            ]);
    }
    /** */

/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function task_status()
    {
        $db_ext=\DB::connection('AA_DWH');
        $order_list = $db_ext->table('OrderToSupplier as o')
        ->select(['orderToSupplier_guid','orderToSupplier','m.description','n.description', 'k.description', 'date', 'manager_guid', 'price', 'quantity', 'СтатьяДДС_guid', 'serviceСomments','Номенклатура_guid','АналитикаРасходов_guid','АналитикаРасходовОрдер_guid'])
        ->join('Менеджеры as m', 'm.guid', '=', 'o.manager_guid')
        ->join('Номенклатура as n', 'n.guid_binary', '=', 'o.Номенклатура_guid')
        ->join('Контрагенты as k', 'k.guid', '=', 'o.provider_guid')
        ->get();
        return mb_convert_encoding($order_list, "UTF-8", "UTF-8");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $tasks = Tasks::find($id);
        if (is_null($tasks)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
        $tasks->delete();
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
