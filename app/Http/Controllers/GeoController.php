<?php
//* Контроллер по Карте
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Validator;
use Adldap\AdldapInterface;
use Illuminate\Support\Facades\DB;
class GeoController extends Controller
{  
    public function show_all($id)
    { 
        $db_ext=\DB::connection('CRM_DWH');
            $district = $db_ext->table('SPR_CLIENT_l as Client')
            ->select('applicantCatoCode','applicantIn','applicantName','LATITUDE_KLIENT','LONGITUDE_KLIENT','LATITUDE_LONGITUDE_OFIS','applicantAddress','NAIMENOVANIE_OFIS','SOURCE')
            ->where('LONGITUDE_KLIENT','!=', '')
            ->where('applicantCatoCode', 'like', substr($id, 0, 2).'%')
            ->distinct('IIN_BIN')
            ->get();
            $client_count=$db_ext->table('SPR_CLIENT_l as Client')
            ->select(DB::raw("COUNT(CASE WHEN Client.SOURCE = 'ERP' THEN 1  END) AS ERP, COUNT(CASE WHEN Client.SOURCE = 'SHIM' THEN 1  END) AS SHIM")
            )
            ->where('applicantCatoCode', 'like', substr($id, 0, 2).'%')
            ->get();
            return response()->json([
                'success'=>false,
                'message' => 'success',
                'count_client'=> $client_count ,
                'data'=>$district
            ]);
    }

    public function aisgzk($id)
    { 
        $db_ext=\DB::connection('CRM_DWH');
            $district = $db_ext->table('CRM_AISGZK_OBLAST_GEO')
            ->select('*')
            ->where('KATO', 'like', substr($id, 0, 2).'%')
            ->get();
           $arr = json_decode($district[0]->GEOMETRY_RINGS);
           $arr2 = json_encode($arr);
            return response()->json([
                'success'=>false,
                'message' => 'success',
                'rings'=> json_decode($district[0]->GEOMETRY_RINGS)
            ]);
    }

    public function show_all_other()
    { 
        $db_ext=\DB::connection('CRM_DWH');
            $district = $db_ext->table('CRM_KOORDINATY_KONTRAGENTA as KK')
            ->select('IIN_BIN','FAKT_ADRESS','longitude','latitude','SOURCE', 'customerName', 'customerAddress')
            ->leftJoin('L1_X.dbo.subsidiesReal', 'sellerIINBIN', '=','IIN_BIN')
            ->where('SOURCE', '=', 'shm')
            ->where('longitude','!=', '')
            ->distinct('IIN_BIN')
            ->get();
            return response()->json([
                'success'=>false,
                'message' => 'success',
                'data'=>$district
            ]);
    }

    public function router_order()
    { 
        $db_ext=\DB::connection('CRM_DWH');
            $district = $db_ext->table('KOORDINATY_KONTRAGENTA_MAPS as KK')
            ->select('GUID','NAIMENOVANIE','SEASON','DATA_OTGRUZKI'
            ,'SPOSOB_DOSTAVKI'
            ,'ADRES_DOSTAVKI'
            ,'SKLAD_OTGRUZKI'
            ,'predstavlenie'
            ,'TO_LONGITUDE'
            ,'TO_LATITUDE'
            ,'FROM_LONGITUDE'
            ,'FROM_LATITUDE'
            ,'SUMMA')
            ->where('SPOSOB_DOSTAVKI', '=', 'Наша транспортная служба до клиента')
            ->where(function($query) {
                $query->where('SKLAD_OTGRUZKI', 'LIKE', '%')
                ->where('DATA_OTGRUZKI', 'LIKE', '%2022%')
                ->whereNotNull('TO_LONGITUDE');
                //->where('MENEDZHER', 'LIKE', '%2022%')
                //->where('AREA', '', '%%');
            })
            ->get();
            return response()->json([
                'success'=>true,
                'message' => 'success',
                'data'=>$district       
            ]);
    }
    
    public function todayorder($name)
    { 
        $db_ext=\DB::connection('CRM_DWH');
            $district = $db_ext->table('DOGOVOR_CRM as KK')
            ->select('MENEDZHER')
            ->where('MENEDZHER', 'like', '%'.$name.'%')
            ->groupby('MENEDZHER')
            ->get();
            return response()->json([
                'success'=>true,
                'message' => 'success',
                'data'=>$district       
            ]);
    }

    public function router_client($guid)
    { 
        $db_ext=\DB::connection('CRM_DWH');
            $district = $db_ext->table('KOORDINATY_KONTRAGENTA_MAPS as KK')
            ->select('GUID','NAIMENOVANIE','SEASON','DATA_OTGRUZKI'
            ,'SPOSOB_DOSTAVKI'
            ,'ADRES_DOSTAVKI'
            ,'SKLAD_OTGRUZKI'
            ,'predstavlenie'
            ,'TO_LONGITUDE'
            ,'TO_LATITUDE'
            ,'FROM_LONGITUDE'
            ,'FROM_LATITUDE'
            ,'SUMMA')
            ->where('SPOSOB_DOSTAVKI', '=', 'Наша транспортная служба до клиента')
            ->where('GUID', '=', $guid)
            ->get();
            return response()->json([
                'success'=>true,
                'message' => 'success',
                'data'=>$district       
            ]);
    }


    
    public function warehouse_aa()
    {
            return response()->json([
                'success'=>true,
                'message' => 'success',
                'data'=>'11'     
            ]);
    }
    public function warehouse_to($guid)
    { 
        $db_ext=\DB::connection('CRM_DWH');
            $district = $db_ext->table('SPR_WAREHOUSE as WH')
            ->select('*')
            ->join('KOORDINATY_KONTRAGENTA_MAPS','SKLAD_OTGRUZKI','=','WH.WAREHOUSE_NAME')
            ->where('WH.GUID', '=', $guid)
            ->where('KOORDINATY_KONTRAGENTA_MAPS.SPOSOB_DOSTAVKI','=', 'Наша транспортная служба до клиента')
            ->get();
            return response()->json([
                'success'=>true,
                'message' => 'success',
                'data'=>$district   
            ]);
    }
    
    //Для возвращение клиентов по области|району Клиентов по кардинатам для вывод маркеров  
    public function show_district($cato)
    {    $formatter=strlen($cato);
         if(strlen($cato)=='9'){
            $substr_cato=substr($cato, 0,2);
            $db_ext=\DB::connection('CRM_DWH');
            $district = $db_ext->table('KOORDINATY_KONTRAGENTA')
            ->select('IIN_BIN','FAKT_ADRESS','longitude','latitude','SOURCE','customerCatoCode', 'sellerName','sellerName','customerName')
            ->join('L1_X.dbo.subsidiesReal', 'sellerIINBIN', '=', 'IIN_BIN')
            ->where('customerCatoCode', 'like', $substr_cato.'%', 'and', '')
            ->orderBy('applicationNumber', 'DESC')
            ->get();
            if ($district->isEmpty()) {
                return response()->json([
                    'success'=>false,
                    'message' => 'Record not found.'
                ], 404);
            }else{
                return response()->json([
                    'success'=>true, 
                    'message'=>'string', 
                    'data'=>$district
                ]);
            }
        }else{
            return response()->json([
                'success'=>false, 
                'message' => 'Не правильный формат КАТО, передающая длина:'.$formatter.'<9.',
            ], 404);
        }
    }
    //конец Маркеров 

    //Регионы КАТО где есть клиент АА//
    public function active_region($kato_id){
        $db_ext = \DB::connection('L1_X');
        $active = $db_ext->table('subsidiesReal ')
        ->select('CustomerCatoCode')
        ->where('CustomerCatoCode', 'like', substr($kato_id, 0, 2).'%')
        ->groupby('CustomerCatoCode')
        ->get();
        $json=array();
        foreach($active as $k){
            $code_k=$k->CustomerCatoCode;
            $f_code=substr($code_k, 0, 4);
            $json[]=$f_code."000000";
        }
        $db_ext2 = \DB::connection('AA_DWH_X'); 
        $dactive = $db_ext2->table('cato')
        ->select('cato_Id')
        ->where('cato_Id', 'NOT like', substr($kato_id, 0, 2).'%')
        ->groupby('cato_Id')
        ->get();
        $disjson=array();
            foreach($dactive as $k){
                $code_k=$k->cato_Id;
                $f_code=substr($code_k, 0, 4);
                $disjson[]=$f_code."000000";
            }
            $object_array["active_cato"] = array_unique($json);
            $object_array["di_active_cato"] = array_unique($disjson);
            return response()->json(['active_cato' => array_unique($json), 'di_active_cato' => array_unique($disjson)]);
                if ($maps->isEmpty()) {
                    return response()->json(['error' => true, 'message' => 'Данные осутствует']);
                }else{
                    return $object_array;
                }
    }

    // Detail data of client Должно из фронта отправлять год данных в статике текущий год//
    public function detail_client($iin, $date)
    {  $formatter=strlen($iin);
         if(strlen($iin)=='12' || !isset($iin)){
            $db_ext=\DB::connection('L1_X');
            $client_detail = $db_ext->table('subsidiesReal as subs')
            ->select('subs.applicationNumber'
            ,'subs.customerName'
            ,'subs.sellerName'
            ,'subs.applicationType'
            ,'subs.applicationStatus'
            ,'subs.quantity'
            ,'subs.quantityUsedToCalculateSubsidiesAmountInUnits'
            ,'subs.usageArea'
            ,'subs.productName'
            ,'subs.customerAddress'
            ,'subs.customerIINBIN'
            ,'subs.customerType'
            ,'subs.customerTelNumber'
            ,'subs.purchasedFrom'
            ,'subs.productUnitDiffGranularity'
            ,'subs.contractSumWithoutIndex_do_not_use'
            ,'subs.contractNumber'
            ,'subs.CropRotationYear'
            ,'subs.sellerIINBIN'
            ,'subs.productType'
            ,'subs.seedReproduction'
            ,'subs.seedCrop'
            ,'subs.flag'
            ,'subs.esfCode'
            ,'subs.efsNoDestribution'
            ,'subs.CustomerCatoCode'
            ,'CLIENT_COORDINATE')
            ->join('CRM_DWH.dbo.KOORDINATY_KONTRAGENTA', 'IIN_BIN', '=', 'customerIINBIN')
            ->where('customerIINBIN', '=', $iin)
            ->where('CropRotationYear', 'like', '%'.$date.'%')
            ->orderBy('applicationNumber', 'DESC')
            ->get();
            if ($client_detail->isEmpty()) {
                return response()->json([
                    'success'=>false,
                    'message' => 'Record not found.'
                ], 404);
            }else{
                return response()->json([
                    'success'=>true, 
                    'message'=>'string', 
                    'data'=>$client_detail
                ]);
            }
        }elseif(strlen($iin)<>'12' || isset($iin)){
            return response()->json([
                'success'=>false, 
                'message' => 'Не правильный формат ИИН, передающая длина:'.$formatter.'<12.',
            ], 404);
        }
    }
      //ENd function Detail data of 

      /*Менеджер подбор*/
      public function manager_region($name)
      {     $formatter=strlen($iin);
            if(strlen($iin)=='12' || !isset($iin)){
              $db_ext=\DB::connection('L1_X');
              $client_detail = $db_ext->table('subsidiesReal as subs')
              ->select('subs.applicationNumber'
              ,'subs.customerName'
              ,'subs.sellerName'
              ,'subs.applicationType'
              ,'subs.applicationStatus'
              ,'subs.quantity'
              ,'subs.quantityUsedToCalculateSubsidiesAmountInUnits'
              ,'subs.usageArea'
              ,'subs.productName'
              ,'subs.customerAddress'
              ,'subs.customerIINBIN'
              ,'subs.customerType'
              ,'subs.customerTelNumber'
              ,'subs.purchasedFrom'
              ,'subs.productUnitDiffGranularity'
              ,'subs.contractSumWithoutIndex_do_not_use'
              ,'subs.contractNumber'
              ,'subs.CropRotationYear'
              ,'subs.sellerIINBIN'
              ,'subs.productType'
              ,'subs.seedReproduction'
              ,'subs.seedCrop'
              ,'subs.flag'
              ,'subs.esfCode'
              ,'subs.efsNoDestribution'
              ,'subs.CustomerCatoCode'
              ,'CLIENT_COORDINATE')
              ->join('CRM_DWH.dbo.KOORDINATY_KONTRAGENTA', 'IIN_BIN', '=', 'customerIINBIN')
              ->where('customerIINBIN', '=', $iin)
              ->orderBy('applicationNumber', 'DESC')
              ->get();
              if ($client_detail->isEmpty()) {
                  return response()->json([
                      'success'=>false,
                      'message' => 'Record not found.'
                  ], 404);
              }else{
                  return response()->json([
                      'success'=>true, 
                      'message'=>'string', 
                      'data'=>$client_detail
                  ]);
              }
          }elseif(strlen($iin)<>'12' || isset($iin)){
              return response()->json([
                  'success'=>false, 
                  'message' => 'Не правильный формат ИИН, передающая длина:'.$formatter.'<12.',
              ], 404);
          }
      }
          /* END Менеджер подбор*/

        /*Менеджер подбор список клиентов по году*/
    public function manager_filter(Request $request)
    {  
        if(strlen($request->iin)=='12' || !isset($iin)){
           $db_ext=\DB::connection('L1_X');
           $client_detail = $db_ext->table('subsidiesReal as subs')
           ->select('subs.applicationNumber'
           ,'subs.customerName'
           ,'subs.sellerName'
           ,'subs.applicationType'
           ,'subs.applicationStatus'
           ,'subs.quantity'
           ,'subs.quantityUsedToCalculateSubsidiesAmountInUnits'
           ,'subs.usageArea'
           ,'subs.productName'
           ,'subs.customerAddress'
           ,'subs.customerIINBIN'
           ,'subs.customerType'
           ,'subs.customerTelNumber'
           ,'subs.purchasedFrom'
           ,'subs.productUnitDiffGranularity'
           ,'subs.contractSumWithoutIndex_do_not_use'
           ,'subs.contractNumber'
           ,'subs.CropRotationYear'
           ,'subs.sellerIINBIN'
           ,'subs.productType'
           ,'subs.seedReproduction'
           ,'subs.seedCrop'
           ,'subs.flag'
           ,'subs.esfCode'
           ,'subs.efsNoDestribution'
           ,'subs.CustomerCatoCode'
           ,'CLIENT_COORDINATE')
           ->join('CRM_DWH.dbo.KOORDINATY_KONTRAGENTA', 'IIN_BIN', '=', 'customerIINBIN')
           ->where('customerIINBIN', '=', $request->iin, 'and', 'CropRotationYear', 'like', $request->date)
           ->orderBy('applicationNumber', 'DESC')
           ->get();
           if ($client_detail->isEmpty()) {
               return response()->json([
                   'success'=>false,
                   'message' => 'Record not found.'
               ], 404);
           }else{
               return response()->json([
                   'success'=>true, 
                   'message'=>'string', 
                   'data'=>$client_detail
               ]);
           }
       }elseif(strlen($request->iin)<>'12' || isset($request->iin)){
           return response()->json([
               'success'=>false, 
               'message' => 'Не правильный формат ИИН, передающая длина:'.strlen($request->iin).'<12.',
           ], 404);
       }
    }

    public function testController(){
        return response()->json([
            'succes'=> true,
            'username' => 'Dauren',
            'firstname' => 'Onglassyn',
            'staff' => 'Manager',
            'age' => '29'
        ]);

    }

    protected function guard() {
        return Auth::guard();
    }
}
