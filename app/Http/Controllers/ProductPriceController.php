<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductPriceController extends Controller
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
        $db_ext = \DB::connection('AA_DWH');
        $productprice = $db_ext->table('Products AS p')
        ->select(['p.productName', 'p.productCode', 'pd.perc_05', 'pd.perc_50', 'pd.perc_95', 'productFormName', 'culturesName', 'manufacturerName'])
        ->join('AA_DWH.dbo.Manufacturer as m', 'p.manufacturer_guid', '=', 'm.manufacturer_guid')
        ->join('AA_DWH_X.dbo.price_distir as pd', 'p.productCode', '=', 'pd.productCode')
        ->join('AA_DWH.dbo.ProductForm as pf', 'pd.productCode', '=', 'pf.productFormCode')
        ->join('AA_DWH.dbo.Cultures as c', 'pf.productFormCode', '=', 'c.culturesCode')
        ->orderBy('productCode')
        ->get();
        return Response($productprice); 
    }

    public function cultures($cutl)
    {
        $db_ext = \DB::connection('AA_DWH');
        $productprice = $db_ext->table('Products AS p')
        ->select(['p.productName', 'p.productCode', 'pd.perc_05', 'pd.perc_50', 'pd.perc_95', 'productFormName', 'culturesName', 'manufacturerName'])
        ->join('AA_DWH.dbo.Manufacturer as m', 'p.manufacturer_guid', '=', 'm.manufacturer_guid')
        ->join('AA_DWH_X.dbo.price_distir as pd', 'p.productCode', '=', 'pd.productCode')
        ->join('AA_DWH.dbo.ProductForm as pf', 'pd.productCode', '=', 'pf.productFormCode')
        ->join('AA_DWH.dbo.Cultures as c', 'pf.productFormCode', '=', 'c.culturesCode')
        ->where('culturesName', 'like', '%'.$cutl.'%')
        ->orderBy('productCode')
        ->get();
        return Response($productprice); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
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
    public function destroy($id)
    {
        //
    }
    protected function guard() {
        return Auth::guard();
    }
}
