<?php

namespace App\Http\Controllers;

use App\Models\Acts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Http\Request;
use App\Http\Resources\ActsResource;


class ActsController extends Controller
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
        $acts = Acts::all();
        //return Response()->json($acts->toArray());
        return ActsResource::collection($acts);
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
            'blank' => 'required|min:3'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $acts = new Acts();
        $acts->blank = json_encode($request->blank);
        $acts->created_at = $request->created_at;
        $acts->save();
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
    public function delete($id)
    {
        $acts = Acts::find($id);
        if (is_null($acts)) {
            return response()->json(['error' => true, 'message' => 'Данный Id отстутсвует'], 404);
        }
        $acts->delete();
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
