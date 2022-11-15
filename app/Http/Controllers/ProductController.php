<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Products;



class ProductController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        //
    }

    public function adress_search($name)
    {
            $add = DB::connection('L1')
            ->table('KONTRAGENTY')
            ->select('IIN_BIN','FAKT_ADRES_KONTRAGENTA')
            ->where('FAKT_ADRES_KONTRAGENTA', 'like', $name.'%')
            ->get();
            return response()->json([
                    'success'=>true, 
                    'message'=>'string', 
                    'data'=>$add
                ]);
    }

    public function product_search($adress_search)
    {
        $products = DB::connection('AA_DWH_X')
        ->table('Products_x')
        ->select('code','name')
        ->where('name', 'like', $name.'%')
        ->get();
        return $products; 
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
        //
    }
    
}
