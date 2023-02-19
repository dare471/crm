<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analyses;
use Validator;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;


class AnalysesController extends Controller
{
    //  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $analyses = Analyses::all();
        return Response($analyses);
    }

    public function create(Request $request)
    {
        $analyses = new Analyses();
        $analyses->provider = $request->provider;
        $analyses->usagearea = $request->usagearea;
        $analyses->cult_1 = $request->cult_1;
        $analyses->cult_1_area = $request->cult_1_area;
        $analyses->cult_2 = $request->cult_2;
        $analyses->cult_2_area = $request->cult_2_area;
        $analyses->cult_3 = $request->cult_3;
        $analyses->cult_3_area = $request->cult_3_area;
        $analyses->created_at = $request->created_at;
        $analyses->save();
        return Response([
            'status' => 'успешный', 
            'statuscode' => 201,
            'data' => 'Успешно создано!' 
        ]);
    }

  
    public function showID($id)
    {
        $analyses = Analyses::where('provider', 'like', "{$id}%")->get(); //join('geos', 'geos.owner', '=', 'contragents.IIN_BIN')->
        if (is_null($analyses)) {
            return response()->json(['error' => true, 'message' => 'Увы такого клиента нет!'], 404);
        }
        return Response($analyses);
    }

    public function update(Request $request, $id)
    {
        $analyses = Analyses::find($id);
        if (is_null($analyses)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
            $analyses->provider = $request->provider;
            $analyses->usagearea = $request->usagearea;
            $analyses->cult_1 = $request->cult_1;
            $analyses->cult_1_area = $request->cult_1_area;
            $analyses->cult_2 = $request->cult_2;
            $analyses->cult_2_area = $request->cult_2_area;
            $analyses->cult_3 = $request->cult_3;
            $analyses->cult_3_area = $request->cult_3_area;
            $analyses->updated_at=$request->updated_at;
            $analyses->save();
            return Response([
                'status' => 'успешный',
                'statuscode' => 201,
                'data' => 'Запись добавлена!'
            ]);
    }

    public function delete($id)
    {
        $analyses = Analyses::find($id);
        if (is_null($analyses)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
        $analyses->delete();
        return Response([
            'status' => 'успешный',
            'statuscode' => 202,
            'data' => 'Успешно удалено'
        ]);
    }
    // protected function guard() {
    //     return Auth::guard();
    // }
}
