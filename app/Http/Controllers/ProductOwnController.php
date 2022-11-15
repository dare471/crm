<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductOwn;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProductOwnController extends Controller
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
        $product_owns = ProductOwn::all();
        return Response ($product_owns);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showID($id)
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

    public function update(Request $request,$id)
    {   
        $rules = [
            'product' => 'required|min:3',
            'product_inf' => 'required|min:3',
            'product_link' => 'required',
        ];
        $validator = validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $product_owns = ProductOwn::find($id);
        if (is_null($product_owns)) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
            $product_owns->product = $request->product;
            $product_owns->product_inf = $request->product_inf;
            $product_owns->product_link = json_encode($request->product_link);
            $product_owns->updated_at=$request->updated_at;
            $product_owns->save();
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
        //
    }

    protected function guard() {
        return Auth::guard();
    }
}
