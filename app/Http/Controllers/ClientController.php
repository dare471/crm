<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show_all_order($id){
        $db_ext= DB::connection('L1');
        $query = $db_ext->table('DOGOVORY_KONTRAGENTOV')
        ->select(DB::raw('CONVERT(NVARCHAR(max), DOGOVORY_KONTRAGENTOV.KONTRAGENT_GUID, 1) dk_guid'), DB::raw('CONVERT(NVARCHAR(max), DOGOVORY_KONTRAGENTOV.GUID, 1) DOGOVOR_GUID'), 'KONTRAGENTY.NAIMENOVANIE AS KONTRAGENT'
        ,'DOGOVORY_KONTRAGENTOV.DATA', 'DOGOVORY_KONTRAGENTOV.SUMMA_KZ_TG','KONTRAGENTY.IIN_BIN'
        ,'DOGOVORY_KONTRAGENTOV.STATUS','DOGOVORY_KONTRAGENTOV.SOSTOYANIE','DOGOVORY_KONTRAGENTOV.NAIMENOVANIE','MENEDZHERY.NAIMENOVANIE AS MENEDZHER','SEZONY.NAIMENOVANIE AS SEZON')
        ->join('MENEDZHERY', 'MENEDZHERY.GUID ','=','DOGOVORY_KONTRAGENTOV.MENEDZHER_GUID')
        ->join('KONTRAGENTY', 'KONTRAGENTY.GUID', '=', 'DOGOVORY_KONTRAGENTOV.KONTRAGENT_GUID')
        ->join('SEZONY', 'SEZONY.GUID', '=', 'DOGOVORY_KONTRAGENTOV.SEZON_GUID')
        ->where('DOGOVORY_KONTRAGENTOV.TIP_DOGOVORA','С покупателем / заказчиком')
        ->where('DOGOVORY_KONTRAGENTOV.DOGOVOR_VNUTRI_GRUPPY', '=', 0)
        ->where('KONTRAGENTY.VKHODIT_V_GRUPPU_KOMPANIY_ALEM_AGRO', 0)
        ->where('KONTRAGENTY.IIN_BIN', $id)
        ->get();
        // //dd($query);
        return response($query);
    }
    public function add($id){
        $db_ext= DB::connection('L1');
        $query = $db_ext->table('DOGOVORY_KONTRAGENTOV')
        ->select(DB::raw('CONVERT(NVARCHAR(max), DOGOVORY_KONTRAGENTOV.KONTRAGENT_GUID, 1) dk_guid'), DB::raw('CONVERT(NVARCHAR(max), DOGOVORY_KONTRAGENTOV.GUID, 1) DOGOVOR_GUID'), 'KONTRAGENTY.NAIMENOVANIE AS KONTRAGENT'
        ,'DOGOVORY_KONTRAGENTOV.DATA', 'DOGOVORY_KONTRAGENTOV.SUMMA_KZ_TG','KONTRAGENTY.IIN_BIN'
        ,'DOGOVORY_KONTRAGENTOV.STATUS','DOGOVORY_KONTRAGENTOV.SOSTOYANIE','DOGOVORY_KONTRAGENTOV.NAIMENOVANIE','MENEDZHERY.NAIMENOVANIE AS MENEDZHER','SEZONY.NAIMENOVANIE AS SEZON')
        ->join('MENEDZHERY', 'MENEDZHERY.GUID ','=','DOGOVORY_KONTRAGENTOV.MENEDZHER_GUID')
        ->join('KONTRAGENTY', 'KONTRAGENTY.GUID', '=', 'DOGOVORY_KONTRAGENTOV.KONTRAGENT_GUID')
        ->join('SEZONY', 'SEZONY.GUID', '=', 'DOGOVORY_KONTRAGENTOV.SEZON_GUID')
        ->where('DOGOVORY_KONTRAGENTOV.TIP_DOGOVORA','С покупателем / заказчиком')
        ->where('DOGOVORY_KONTRAGENTOV.DOGOVOR_VNUTRI_GRUPPY', '=', 0)
        ->where('KONTRAGENTY.VKHODIT_V_GRUPPU_KOMPANIY_ALEM_AGRO', 0)
        ->where('KONTRAGENTY.IIN_BIN', $id)
        ->get();
        // //dd($query);
        return response($query);
    }
    public function client_list(){
        $db_ext = DB::connection('CRM_DWH');
        $query = $db_ext->select("SELECT TOP (1000) 
		[NAME]
        ,[IIN_BIN]
		, CONVERT(NVARCHAR(max), [GUID], 1) dk_guid
		,[ADDRESS]
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS  CCI 
        LEFT JOIN CRM_DWH.DBO.CRM_CLIENT_ID_GUID AS CCIG ON CCI.CLIENT_ID=CCIG.ID
        LEFT JOIN CRM_DWH.DBO.CRM_CLIENT_PROPERTIES AS CCR ON CCR.CLIENT_INFO_ID = CCIG.ID
        WHERE [IIN_BIN]!='' AND GUID!='' AND CCR.COORDINATES!='' 
		GROUP BY [NAME]
        ,[IIN_BIN]
		,[GUID]
		,[ADDRESS]
		ORDER BY IIN_BIN, ADDRESS  DESC"); 
        return response($query);
    }
    
}
