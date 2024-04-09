<?php
namespace App\Http\Controllers;
use App\Models\Parser;
use app\Models\Coordinates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Mail;
use App\Http\Resources\DogovorResource;


class UtilXMLController extends Controller 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verification(Request $request)
    {  
        //return $request->iin; 
        if(!empty($request->iin)){
            if(strlen($request->iin)<'12') {
                return response()->json([
                    'success'=>false, 
                    'message'=>'string', 
                    'data'=>'Введённый ИИН меньше/больше стандартного, длина введённого ИИН состовляет: '.strlen($request->iin)
                ]);
            }
            else{
                $dbx=DB::connection('L1')
                ->table('FIZICHESKIE_LITSA')
                ->select('I_N_N','FAMILIYA','IMYA')
                ->where('I_N_N', $request->iin)
                ->get();
                if($dbx->isNotEmpty()){
                    return response()->json([
                        'succes'=>true,
                        'data'=>$dbx
                    ]);
                }
                else {
                    return response()->json([
                        'success'=>false, 
                        'message'=>'string', 
                        'data'=>'Такого пользователя не существует, проверить правильность ввода ИИН '.$request->iin
                    ]);
                }
            }
        }
        else{
            return response()->json([
                'success'=>false, 
                'message'=>'string', 
                'data'=>'Такого пользователя не существует, проверить правильность ввода ИИН '.$request->iin
            ]);
        }
    }

    protected function esf(Request $request) {    
        $file=$request->file;
        $iin=$request->iin;
        if($file->getClientOriginalExtension()=='xml'){
            $destinationPath = 'uploads';
            $file->move($destinationPath,$date = $date = date('m-d-Y-h:i:s', time())."-".$request->company."-".$file->getClientOriginalName());
            $xmlString = file_get_contents(public_path('/uploads/'.$date = date('m-d-Y-h:i:s', time())."-".$request->company."-".$file->getClientOriginalName()));
            $xmlObject = simplexml_load_string($xmlString);
            $json = json_encode($xmlObject);
            $arrayxml = json_decode($json, true);
            foreach ($arrayxml as $value){
                $esf_app_body=$value['invoiceInfo']['invoiceBody'];
                $esf_id=$value['invoiceInfo']['registrationNumber'];
                $esf_add=$value['invoiceInfo']['registrationNumber'];
            }
            $esf_all=DB::connection('CRM_DWH')
                ->table('Util_ESF')
                ->select('CLIENT_IIN_BIN','ESF_REGISTRATSIONNYY_NOMER')
                ->where('ESF_REGISTRATSIONNYY_NOMER', '=', $esf_id)
                ->get();
                foreach ($esf_all as $value) {
                    $inn=$value->CLIENT_IIN_BIN;
                }
            DB::connection('CRM_DWH')
                ->table('UTIL_APPLICATION_TICKETS')
                ->insert(
                ['IIN_APPLICATION' =>  $iin, 'ESF_REG_NUMBER' => $esf_id, 'XML_BODY' => $json, 'DATE_CREATE' => Carbon::now()->toDateTimeString()]
                );

            if(!$esf_all->isEmpty()){
                return response()->json([
                    'success'=>true, 
                    'message'=>'Счет фактура присуствует в системе, заявка распарсенна.', 
                    'esf_id'=>$esf_id,
                    'data'=>$esf_all
                ]);
            }
            else{
                return response()->json([
                    'success'=>false, 
                    'message'=>'Счет фактура осутствует в системе, заявка аннулированна.', 
                ]);
            }
            
        }
        else{
            return response()->json([
                'success'=>false, 
                'message'=>'Не тот формат файла'
            ]);
        }
    }

    protected function all_esf()
    { 
        $esf_all=DB::connection('CRM_DWH')
        ->table('UTIL_APPLICATION_TICKETS')
        ->select('ID','IIN_APPLICATION as ИИН','CLIENT_NAME as Наименование_хозяйства','ESF_REG_NUMBER as Номер','DATE_CREATE as Дата_загрузки')
        ->join('Util_ESF', 'ESF_REGISTRATSIONNYY_NOMER', '=', 'ESF_REG_NUMBER')
        ->distinct('ESF_REG_NUMBER')
        ->limit(15)
        ->get();
        return $esf_all;
    }
    protected function view_utils(Request $request)
    { 
        $esf=$request->esf;
        $util_esf=DB::connection('CRM_DWH')
            ->table('Util_ESF')
            ->select('PRODUCT_NAME','TOTAL','DOGOVOR_NOMER','PROIZVODITELI','POSTAVSСHIK_IIN_BIN','POSTAVSСHIK_NAME')
            ->where('ESF_REGISTRATSIONNYY_NOMER','=', $esf)
            ->get();

        $sec_tab=DB::connection('CRM_DWH')
            ->table('Util_ESF')
            ->select('ESF_REGISTRATSIONNYY_NOMER','CLIENT_IIN_BIN','CLIENT_NAME')
            ->where('ESF_REGISTRATSIONNYY_NOMER', '=', $esf)
            ->groupby('ESF_REGISTRATSIONNYY_NOMER','CLIENT_IIN_BIN','CLIENT_NAME')   
            ->get();

        return response()->json([
            'success'=>true,
            'client_name'=>$sec_tab,
            'data_esf'=>$util_esf
        ]);
    }

    public function email_send($id)
    { 
        $esf_all=DB::connection('CRM_DWH')
        ->table('UTIL_APPLICATION_TICKETS')
        ->select('ID', 'Util_ESF.CLIENT_IIN_BIN', 'ESF_REG_NUMBER','Util_ESF.CLIENT_NAME', 'PRODUCT_NAME','COUNT_IN', 'COUNT_OUT', 'TOTAL', 'DOGOVOR_NOMER', 'PROIZVODITELI','POSTAVSСHIK_NAME')
        ->join('Util_ESF', 'ESF_REGISTRATSIONNYY_NOMER', '=', 'ESF_REG_NUMBER')
        ->where('ID', '=', $id)
        ->get();
        return $esf_all;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function coordinate_to_from(Request $request){
            $to = urlencode($request->to);
            $from = urlencode($request->from);

            $to_coord = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$to.'&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $to_coordinate = json_decode($to_coord, true);
            $t=$to_coordinate['results'][0]['geometry']['location'];

            $from_coord = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$from.'&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $from_coordinate = json_decode($from_coord, true);
            $f=$from_coordinate['results'][0]['geometry']['location'];

            $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?&origins='.$to.'&destinations='.$from.'&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
            $distance_arr = json_decode($distance_data);
            //return $distance_arr;
            return response()->json([
                "origin_addresses" => $distance_arr->destination_addresses[0],
                "to_coordinate" => $t,
                "destination_addresses" => $distance_arr->origin_addresses[0],
                "from_coordinate" => $f,
                "distance" => $distance_arr->rows[0]->elements[0]->distance->value,
                "duration" => $distance_arr->rows[0]->elements[0]->duration->value,
            ]);

        }

        public function coordinate_f_ele(){
            $db_ext = DB::connection('CRM_DWH');
            $coordinates = $db_ext->table('CRM_ELEVATOR')
            ->get();
            foreach($coordinates as $c) {
                $to = urlencode($c->LOCATION);
                $from_coord = file_get_contents('https://maps.google.com/maps/api/geocode/json?address="'.$to.'"&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo');
                $from_coordinate = json_decode($from_coord, true);
                $res = $from_coordinate['results'];
                foreach($res as $fc){
                $db_ext->table('CRM_ELEVATOR')
                    ->where('ID', '=', $c->ID)
                    ->update([
                        'LATITUDE' => $fc['geometry']['location']['lat'],
                        'LONGITUDE' => $fc['geometry']['location']['lng'],
                    ]);
                }
            }
            
        }

        public function dg_guid(Request $request){
            
            $dbx=DB::connection('L1')
            ->table('DOGOVORY_KONTRAGENTOV as DK')
            ->select(DB::raw("CONVERT(VARCHAR(1000), GUID, 1) as GUID"),'DK.NAIMENOVANIE','NOMER','DK.DATA_NACHALA_DEYSTVIYA'
            ,'DK.DATA_OKONCHANIYA_DEYSTVIYA'
            ,'DK.DATA'
            ,'DK.SUMMA'
            ,'DK.TIP_DOGOVORA'
            ,'DK.USLOVIYA_OPLATY'
            ,'DK.SPOSOB_DOSTAVKI','ADRES_DOSTAVKI')
            ->limit(2)
            ->get();
            return $dbx;            
        }

        public function dg(Request $request){
        
            $dbx=DB::connection('L1')
                ->table('DOGOVORY_KONTRAGENTOV as DK')
                ->select(DB::raw("CONVERT(VARCHAR(1000), DK.GUID, 1) as GUID"),'DK.NAIMENOVANIE','s.naimenovanie','NOMER','SPD.DATA_OTGRUZKI','DK.DATA_NACHALA_DEYSTVIYA'
                ,'DK.DATA_OKONCHANIYA_DEYSTVIYA'
                ,'DK.DATA'
                ,'DK.SUMMA'
                ,'DK.TIP_DOGOVORA'
                ,'DK.USLOVIYA_OPLATY'
                ,'DK.SPOSOB_DOSTAVKI','ADRES_DOSTAVKI','SPD.SKLAD_OTGRUZKI', 'TB.predstavlenie')
                ->join('SPETSIFIKATSIYA_PO_DOGOVORU as SPD', 'SPD.DOGOVOR_GUID', '=', 'DK.GUID')
                ->join('L0.dbo.tab_kontaktnaya_informatsiya_sklady as TB', 'SPD.SKLAD_OTGRUZKI_GUID', '=', 'TB.ssylka')
                ->join('SEZONY as s', 'DK.SEZON_GUID', '=', 's.GUID')
                ->where(DB::raw('DK.GUID'),'=', '')
                ->distinct('NAIMENOVANIE')
                ->orderByDesc('DATA_OTGRUZKI')
                ->toSql();
            return $dbx;
        }
    }
