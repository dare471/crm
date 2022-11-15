<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MapsDataController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $db_ext = \DB::connection('L1');
        $cult_t = $db_ext->table('TOP_CULT_CRM')
        ->select('PROVIDER', DB::raw('SUM(USAGE_AREA) as USAGE_AREA'), DB::raw('SUM(COUNT_CLIENT) as COUNT_CLIENT'))
        ->groupby('PROVIDER')
        ->get();
        return Response($cult_t);
    }
    

    public function region_detail($id)
    {
        $db_ext=\DB::connection('L1');
        $cult_t = $db_ext->table('TOP_CULT_CRM')
        ->select(['TOP_CULT_CRM.provider','TOP_CULT_CRM.COUNT_CLIENT','TOP_CULT_CRM.USAGE_AREA','TOP_CULT_CRM.cato_id', 'CULT1_NAME', 'CULT1_Area', 'CULT2_NAME', 'CULT2_Area', 'CULT3_NAME', 'CULT3_Area' ])
        ->where('TOP_CULT_CRM.cato_id', '=', "$id")
        ->orderBy('TOP_CULT_CRM.provider', 'DESC')
        ->get();
        return Response($cult_t);
    }

    public function maps_r_kato($kato_id){
        $db_ext = \DB::connection('AA_DWH');
        $maps = $db_ext->table('ДоговорыКонтрагентов as dk')
        ->join('Менеджеры as m', 'm.guid', '=', 'dk.Менеджер_guid')
        ->join('Контрагенты as K', 'K.guid', '=', 'dk.Контрагент_guid')
        ->join('CRM_DWH.dbo.KOORDINATY_KONTRAGENTA as kk', 'kk.guid', '=', 'dk.Контрагент_guid')
        ->join('Организации as O', 'O.guid', '=', 'dk.Организация_guid')
        ->join('БизнесРегионы as BR', 'BR.guid', '=', 'dk.БизнесРегион_guid')
        ->join('Сезоны as S', 'S.guid', '=', 'dk.Сезон_guid')
        ->leftjoin('Контрагенты as KA', 'KA.guid_partner', '=', 'dk.Агент_guid')
        ->leftjoin('ТоварыЗаказаКлиента as tzk', 'tzk.ЗаказКлиента_guid', '=', 'dk.ЗаказКлиента_guid')
        ->select(
        'kk.CLIENT_COORDINATE'
        ,'kk.FAKT_ADRESS'
        ,'O.description as provider'
        ,'K.kato'
        ,'MOTHER_OFFICE'
        ,'K.description as client'
        ,'kk.IIN_BIN'
        ,'m.description as manager_fio'
        ,'BR.description as Businesse_Region'
        ,'s.description as season'
        ,'Сумма')
        // ->select(DB::raw('count(distinct dk.Контрагент_guid) as client_count, m.description as manager_fio', 'k.kato'))
        ->where('k.kato', 'like', substr($kato_id, 0, 2).'%')
        ->groupby('kk.CLIENT_COORDINATE'
        ,'kk.FAKT_ADRESS'
        ,'O.description'
        ,'K.kato'
        ,'MOTHER_OFFICE'
        ,'K.description'
        ,'kk.IIN_BIN'
        ,'m.description'
        ,'BR.description'
        ,'s.description'
        ,'Сумма')
        ->get();
        if ($maps->isEmpty()) {
            return response()->json(['error' => true, 'message' => 'Данные осутствует'], 404);
        }
        return Response($maps);
    }

    /// Удалить 

    public function semena(){
        $db_ext = \DB::connection('CRM_DWH');
        $semena = $db_ext->table('2_SEMENA_TEST')
        ->select(DB::raw('Convert(NVARCHAR(max), nomenklatura_guid, 1) id'),'Компании','Культура','Регион','Название продукта','Технология','Подтверждение','Остаток','Продано','Отгружено','Не отгружено','Стоки 2021','Поступление', DB::raw('Convert(NVARCHAR(max), direksiya, 1) direksiya'))
        //->where('Регион', '!=', '')
        ->get();
        return response($semena);
    }
    public function semena_wr(Request $request){
        $db_ext = \DB::connection('CRM_DWH');
        $semena = \DB::connection('CRM_DWH')->statement('EXEC [CRM_DWH].[dbo].UPDATE_2_SEMENA_TEST_PODVERZHDENIE
        @nomenklatura_guid='.$request->nomenklatura_guid.'  
        ,@direksiya='.$request->direksiya.'
        ,@POTVERZHDENIE='.$request->potr.'');
        return response()->json(['status' => true, 'message' => 'success'], 200);
        
    }
    
    //
    

    //Активные като id где есть договора
    public function active_r($kato_id){
        $db_ext=\DB::connection('L1');
        $db_ext = \DB::connection('AA_DWH');
        $maps = $db_ext->table('ДоговорыКонтрагентов as dk')
        ->join('Менеджеры as m', 'm.guid', '=', 'dk.Менеджер_guid')
        ->join('Контрагенты as K', 'K.guid', '=', 'dk.Контрагент_guid')
        ->select('k.kato')
        ->where('k.kato', 'like', substr($kato_id, 0, 2).'%')
        ->groupby('k.kato')
        ->get();
         $json=array();
        foreach($maps as $k){
            $code_k=$k->kato;
            $f_code=substr($code_k, 0, 4);
            $json[]=$f_code."000000";
        }
        $object_array ["kato_active"] = array_unique($json);
        if ($maps->isEmpty()) {
            return response()->json(['error' => true, 'message' => 'Данные осутствует'], 404);
        }
        return $object_array;
    }

    //Список договоров по клиенту

    public function manager_data_client($iin_c){
        $db_ext = \DB::connection('AA_DWH');
        $maps = $db_ext->table('ДоговорыКонтрагентов as dk')
        ->select(
        'latitude'
        ,'longitude'
        ,'kk.MOTHER_OFFICE'
        ,'K.description as client'
        ,'kk.IIN_BIN'
        ,'M.description'
        )
        ->join('Менеджеры as M', 'm.guid', '=', 'dk.Менеджер_guid')
        ->join('Контрагенты as K', 'K.guid', '=', 'dk.Контрагент_guid')
        ->join('CRM_DWH.dbo.KOORDINATY_KONTRAGENTA as kk', 'kk.guid', '=', 'K.guid')
        ->join('Организации as O', 'O.guid', '=', 'dk.Организация_guid')
        ->join('БизнесРегионы as BR', 'BR.guid', '=', 'dk.БизнесРегион_guid')
        ->join('Сезоны as S', 'S.guid', '=', 'dk.Сезон_guid')
        ->leftjoin('Контрагенты as KA', 'KA.guid_partner', '=', 'dk.Агент_guid')
        ->leftjoin('ТоварыЗаказаКлиента as tzk', 'tzk.ЗаказКлиента_guid', '=', 'dk.ЗаказКлиента_guid')
        ->where('IIN_BIN', '=', $iin_c)
        ->where('longitude', '!=', '')
        ->groupby( 
        'latitude'
        ,'longitude'
        ,'kk.MOTHER_OFFICE'
        ,'K.description'
        ,'kk.IIN_BIN','M.description')
        ->get();
        if ($maps->isEmpty()) {
            return response()->json(['error' => true, 'message' => 'Данные осутствует'], 404);
        }
        return Response($maps);
    }

    public function manager_data_client_detail($iin){
        $db_ext = \DB::connection('AA_DWH');
        $maps = $db_ext->table('ДоговорыКонтрагентов as dk')
        ->select('ДатаНачалаДействия as dnd'
        ,'ДатаОкончанияДействия as dod'
        ,'dk.description as number_dok'
        ,'O.description as provider'
        ,'K.kato'
        ,'kk.MOTHER_OFFICE'
        ,'K.description as client'
        ,'kk.IIN_BIN'
        ,'m.description as manager_fio'
        ,'BR.description as Businesse_Region'
        ,'s.description as season'
        ,'Сумма')
        //,DB::raw('(CASE WHEN УсловияОплаты IS NULL THEN "0" ELSE УсловияОплаты END) AS Condition_Payment'))
        ->join('Менеджеры as M', 'm.guid', '=', 'dk.Менеджер_guid')
        ->join('Контрагенты as K', 'K.guid', '=', 'dk.Контрагент_guid')
        ->join('CRM_DWH.dbo.KOORDINATY_KONTRAGENTA as kk', 'kk.guid', '=', 'K.guid')
        ->join('Организации as O', 'O.guid', '=', 'dk.Организация_guid')
        ->join('БизнесРегионы as BR', 'BR.guid', '=', 'dk.БизнесРегион_guid')
        ->join('Сезоны as S', 'S.guid', '=', 'dk.Сезон_guid')
        ->leftjoin('Контрагенты as KA', 'KA.guid_partner', '=', 'dk.Агент_guid')
        ->leftjoin('ТоварыЗаказаКлиента as tzk', 'tzk.ЗаказКлиента_guid', '=', 'dk.ЗаказКлиента_guid')
        ->where('kk.IIN_BIN', '=', $iin)
        ->get();
        if ($maps->isEmpty()) {
            return response()->json(['error' => true, 'message' => 'Данные осутствует'], 404);
        }
        return Response($maps);
    }

    public function client_inf($client_iin){
        $db_ext = \DB::connection('AA_DWH');
        $maps = $db_ext->table('ДоговорыКонтрагентов as dk')
        ->select('dk.description as number_dok'
        ,'ДатаНачалаДействия as dnd'
        ,'ДатаОкончанияДействия as dod'
        ,'O.description as provider'
        ,'K.kato'
        ,'kk.IIN_BIN'
        ,'K.description as client'
        ,'m.description as manager_fio'
        ,'BR.description as Businesse_Region'
        ,'s.description as season'
        ,'Сумма'
        ,'ТипДоговора'
        ,'Автосогласование'
        ,'m.source_base'
        ,'СтавкаНДС'
        ,'СтавкаНСП'
        ,'ВидСтавкиНДС'
        ,'БезналичныйРасчет'
        ,'СуммаАгентских'
        ,'ПроцентАгентских'
        ,'ВалютаВзаиморасчетов'
        ,'СтатусПодписания'
        ,'Статус'
        ,'СпособДоставки'
        ,'АдресДоставки'
        ,'Состояние')
        ->join('Менеджеры as M', 'm.guid', '=', 'dk.Менеджер_guid')
        ->join('Контрагенты as K', 'K.guid', '=', 'dk.Контрагент_guid')
        ->join('CRM_DWH.dbo.KOORDINATY_KONTRAGENTA as kk', 'kk.guid', '=', 'dk.Контрагент_guid')
        ->join('Организации as O', 'O.guid', '=', 'dk.Организация_guid')
        ->join('БизнесРегионы as BR', 'BR.guid', '=', 'dk.БизнесРегион_guid')
        ->join('Сезоны as S', 'S.guid', '=', 'dk.Сезон_guid')
        ->leftjoin('Контрагенты as KA', 'KA.guid_partner', '=', 'dk.Агент_guid')
        ->leftjoin('ТоварыЗаказаКлиента as tzk', 'tzk.ЗаказКлиента_guid', '=', 'dk.ЗаказКлиента_guid')
        ->where('kk.IIN_BIN', '=', $client_iin)
        ->get();
        if ($maps->isEmpty()) {
            return response()->json(['error' => true, 'message' => 'Данные осутствует'], 404);
        }
        return Response($maps);  

    }

    public function maps_data(){
        $db_ext = \DB::connection('AA_DWH');
        $maps = $db_ext->table('ДоговорыКонтрагентов as dk')
        ->select('dk.description as number_dok','ДатаНачалаДействия as dnd','ДатаОкончанияДействия as dod','O.description as organization','K.kato','K.description as order_c','kk.IIN_BIN'
        ,'kk.CLIENT_COORDINATE as coordinate','m.description as manager_fio','BR.description as Businesse_Region','s.description as season','Сумма','ТипДоговора','Автосогласование'
        ,'m.source_base'
        ,'СтавкаНДС'
        ,'СтавкаНСП'
        ,'ВидСтавкиНДС'
        ,'БезналичныйРасчет'
        ,'СуммаАгентских'
        ,'ПроцентАгентских'
        ,'ВалютаВзаиморасчетов'
        ,'СтатусПодписания'
        ,'Статус'
        ,'СпособДоставки'
        ,'АдресДоставки'
        ,'Состояние'
        )
        ->join('Менеджеры as M', 'm.guid', '=', 'dk.Менеджер_guid')
        ->join('Контрагенты as K', 'K.guid', '=', 'dk.Контрагент_guid')
        ->join('CRM_DWH.dbo.KOORDINATY_KONTRAGENTA as kk', 'kk.guid', '=', 'dk.Контрагент_guid')
        ->join('Организации as O', 'O.guid', '=', 'dk.Организация_guid')
        ->join('БизнесРегионы as BR', 'BR.guid', '=', 'dk.БизнесРегион_guid')
        -> join('Сезоны as S', 'S.guid', '=', 'dk.Сезон_guid')
        ->leftjoin('Контрагенты as KA', 'KA.guid_partner', '=', 'dk.Агент_guid')
        ->leftjoin('ТоварыЗаказаКлиента as tzk', 'tzk.ЗаказКлиента_guid', '=', 'dk.ЗаказКлиента_guid')
        ->get();
        if (is_null($maps)) {
            return response()->json(['error' => true, 'message' => 'Данные осутствует'], 404);
        }
       return Response($maps);
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
        $db_ext = \DB::connection('L1');
        $cult_t = $db_ext->table('TOP_CULT_CRM')
        ->select(['PROVIDER','CATO','USAGEAREA','COUNT_CLIENT','CULT1_NAME','CULT1_AREA','CULT2_NAME','CULT2_AREA','CULT3_NAME','CULT3_AREA'])
        ->where('TOP_CULT_CRM.cato_id', '=', "$id")
        ->orderBy('TOP_CULT_CRM.provider', 'ASC')
        ->get();

        $product_t = $db_ext->table('TOP_PROD_CRM')
        ->select(['PROVIDER','CATO','VSE_ZIAVKI','CATO_ID','PROD1_NAME','PROD1_KOLICHESTVO','PROD2_NAME','PROD2_KOLICHESTVO','PROD3_NAME','PROD3_KOLICHESTVO'])
        ->where('cato_id', '=', "$id")
        ->orderBy('provider', 'ASC')
        ->get();

        if ($cult_t->isEmpty()|| $product_t->isEmpty()) {
            return response()->json(['error' => true, 'message' => 'Данный ID осутствует'], 404);
        }
        return ["cult"=>$cult_t, "product"=>$product_t];

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

    // protected function guard() {
    //     return Auth::guard();
    // }
}
