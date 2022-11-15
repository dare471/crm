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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showID($id)
    {
        $analyses = Analyses::where('provider', 'like', "{$id}%")->get(); //join('geos', 'geos.owner', '=', 'contragents.IIN_BIN')->
        if (is_null($analyses)) {
            return response()->json(['error' => true, 'message' => 'Увы такого клиента нет!'], 404);
        }
        return Response($analyses);
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

    //** POWER BI REPORT CONTROLLER PART START */
    public function report_all()
    {
        $db_ext = DB::connection('CRM_DWH');
        $rep = $db_ext->table('CRM_REPORT_LIST_AA')
        ->select('*')
        ->get();
        return response([
            'status' => 'Success',
            'status' => 201,
            'data' => $rep
        ]);
    }

    public function report_detail($id){
        $db_ext = DB::connection('CRM_DWH');
        $query = $db_ext->select("SELECT [id]
        ,[NAME]
        ,[LINK]
        ,[GROUP]
        FROM [CRM_DWH].[dbo].[CRM_REPORT_LIST_AA] WHERE id=$id");
        return response($query);
    }

    public function report_insert(Request $request)
    {
        $db_ext = DB::connection('CRM_DWH');
        $rep = $db_ext->table('CRM_REPORT_LIST_AA')
        ->insert([
            'NAME' => $request->name,
            'LINK' => $request->powerbi_link,
            'GROUP' => $request->group
        ]);

        return response([
            'status' => 'Success',
            'status' => 201,
            'data' => $request->name
        ]);
    }
    public function topmanager(){
        $top = DB::select(DB::raw("SELECT TOP (1)M.NAIMENOVANIE [MENEDZHER_GUID],SUM([SUMMA])[SUMMA] FROM [L1].[dbo].[DOGOVORY_KONTRAGENTOV] DK LEFT JOIN L1.DBO.KONTRAGENTY K ON DK.KONTRAGENT_GUID=K.GUID LEFT JOIN L1.DBO.MENEDZHERY M ON DK.MENEDZHER_GUID=M.GUID WHERE 1=1 AND K.[VKHODIT_V_GRUPPU_KOMPANIY_ALEM_AGRO]=0 AND DOGOVOR_VNUTRI_GRUPPY=0 AND DK.SEZON_GUID=0xAF83D4F5EF10792511EBE2FB064EBC27 AND DK.TIP_DOGOVORA='С покупателем / заказчиком' AND M.NAIMENOVANIE NOT IN ('Дакариева Жанна','Абдыкадыров Амангельды') GROUP BY M.NAIMENOVANIE ORDER BY [SUMMA] DESC"));
    return response($top);
    }
    public function analytics(){
        $analyses = DB::table('QOLDAU')
        ->get();
        return response($analyses);
    }

    public function report_update(Request $request)
    {
        $db_ext = DB::connection('CRM_DWH');
        $rep = $db_ext->table('CRM_REPORT_LIST_AA')
        ->where('id', $request->id)
        ->update([
            'NAME' => $request->name,
            'LINK' => $request->powerbi_link,
            'GROUP' => $request->group
        ]);

        return response([
            'status' => 'Success',
            'status' => 201,
            'data' => $request->name
        ]);
    }

    public function report_delete(Request $request)
    {
        $db_ext = DB::connection('CRM_DWH');
        $rep = $db_ext->table('CRM_REPORT_LIST_AA')
        ->where('id', $request->id)
        ->delete();

        return response([
            'status' => 'Success',
            'status' => 201,
            'data' => 'that"s id deleted'
        ]);
    }



    //** POWER BI REPORT CONTROLLER PART END */
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
