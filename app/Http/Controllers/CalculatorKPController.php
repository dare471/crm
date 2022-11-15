<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalculatorKP;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CalculatorKPResource;


class CalculatorKPController extends Controller
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
        $calculator_kp = CalculatorKP::all();
        //$calculator_kp = $this->user->calculator_kp()->get(['id', 'body']);
        return CalculatorKPResource::collection($calculator_kp);
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
            'body' => 'required|min:3'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $calculator_kp = new CalculatorKP();
        $calculator_kp->body = json_encode($request->body);
        $calculator_kp->user_id = $request->user_id;
        $calculator_kp->created_at = $request->created_at;
        $calculator_kp->save();
        return Response([
            'status' => 'успешный', 
            'statuscode' => 201,
            'data' => 'Успешно создано!' 
        ]);
        //return response()->json($calculator_kp, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    

    public function name_c($name)
    {
        $db_ext = \DB::connection('pgsql');
        $cult_t = $db_ext->table('pesticides')
        ->where('culture', 'like', '%'.$name.'%')
        ->select('culture')
        ->orderbyDesc('culture')
        ->groupby('culture')
        ->get();
        $str=preg_replace('/\s+/', ' ', $cult_t);
        return Response($str);
    }
    public function name_p($name_c)
    {
        $db_ext = \DB::connection('pgsql');
           if (isset($name_c)) {
            $cult_t = $db_ext->table('pesticides')
            ->where('culture', '=', $name_c)
            ->select('culture','tradename','consumption_preparation','time_processing')
            ->get();
            $str=preg_replace('/\s+/', ' ', $cult_t);
            return Response($str);
           }
           else{
            return response()->json(['error' => true, 'message' => 'Данная запись осутствует'], 404);
           }
            
    }

    public function joint()
    {
        $db_ext = \DB::connection('pgsql');
        $db_j = \DB::connection('AA_DWH');
            $cult_t = DB::table('pesticides as pst')
            ->join($db_j->table('products'), 'products.productName', 'LIKE', '%pst.tradename%')
            ->get();
            $str=preg_replace('/\s+/', ' ', $cult_t);
            return Response($str);
    }
    

    public function cult_all()
    {
        $db_ext = \DB::connection('pgsql');
        $cult_t = $db_ext->table('pesticides')
        ->select('culture')
        ->groupby('culture')
        ->get();
        $str=preg_replace('/\s+/', ' ', $cult_t);
        return Response($str);
    }

    public function pesticides_types()
    {
        $db_ext = \DB::connection('pgsql');
        $p_t = $db_ext->table('pesticides_types')
        ->get();
        $str=preg_replace('/\s+/', ' ', $p_t);
        return Response($str);
    }

    public function object()
    {
        $db_ext = \DB::connection('pgsql');
        $p_t = $db_ext->table('pesticides_types')
        ->get();
        $str=preg_replace('/\s+/', ' ', $p_t);
        return Response($str);
    }

    public function pesticides_f($id)
    {
        $db_ext = \DB::connection('pgsql');
        $p_t = $db_ext->table('pesticides_types')
        ->where('pest_id', '=', $id)
        ->get();
        $str=preg_replace('/\s+/', ' ', $p_t);
        return Response($str);
    }

    public function object_v()
    {
        $db_ext = \DB::connection('pgsql');
        $p_t = $db_ext->table('pesticides')
        ->select('harmful_organism')
        ->groupby('harmful_organism')
        ->get();
        $str=preg_replace('/\s+/', ' ', $p_t);
        return Response($str);
    }
    public function season()
    {
        $db_ext = \DB::connection('pgsql');
        $p_t = $db_ext->table('pesticides')
        ->select('time_processing')
        ->groupby('time_processing')
        ->get();
        $str=preg_replace('/\s+/', ' ', $p_t);
        return Response($str);
    }

    public function showID($user_id)
    {
        $calculator_kp = CalculatorKP::where('user_id', $user_id)->get();
        if (is_null($calculator_kp)) {
            return response()->json(['error' => true, 'message' => 'Данная запись осутствует'], 404);
        } 
        return Response($calculator_kp);
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
            'body' => 'required|min:3'
        ];
        $validator = validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $calculator_kp = CalculatorKP::find($id);
        if (is_null($calculator_kp)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
            $calculator_kp->body = json_encode($request->body);
            $calculator_kp->updated_at=$request->updated_at;
            $calculator_kp->save();
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
    public function delete($id)
    {
        $calculator_kp = CalculatorKP::find($id);
        if (is_null($calculator_kp)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
        $calculator_kp->delete();
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
