<?php

namespace App\Http\Controllers;

use App\Models\Coordinates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoordinatesController extends Controller
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
        $db_ext = \DB::connection('CRM_DWH');
        $coordinate = $db_ext->table('KOORDINATY_KONTRAGENTA_MAPS')
        ->select('SPOSOB_DOSTAVKI','ADRES_DOSTAVKI','FROM_LONGITUDE','FROM_LATITUDE','TO_LONGITUDE','TO_LATITUDE','NAIMENOVANIE','predstavlenie')
        ->where('SPOSOB_DOSTAVKI', '=', 'Наша транспортная служба до клиента')
        ->whereNull('TO_LONGITUDE')
        ->get();
        foreach($coordinate as $c) {
            $add=$c->predstavlenie;
            $name=$c->NAIMENOVANIE;
            $conv=urlencode($add);
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://maps.google.com/maps/api/geocode/json?address='.$conv.'&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1500,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $res = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($res, true);
            if(empty($res['results'])){
                $dataltd='NULL';
                $datalng='NULL';
            }
            else{
                $coor = $res['results'][0]['geometry']['location'];
                $datalng=$coor['lng'];
                $dataltd=$coor['lat'];
            }
                $koor=$db_ext->table('KOORDINATY_KONTRAGENTA_MAPS')
                ->where('NAIMENOVANIE', '=', $name)
                ->update([
                    'TO_LONGITUDE' => $datalng,
                    'TO_LATITUDE' =>$dataltd
                ]);     
         $log  = "NAME: ".$name.' - '.date("F j, Y, g:i a").PHP_EOL.
        "Attempt: ".$datalng." - ".$dataltd." ".PHP_EOL.
        "-------------------------".PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents('/var/www/crm/log_COORDINATES_'.date("j.n.Y").'.log', $log, FILE_APPEND);
        }
    }

    public function coordinate_warehouse()
    {
        $db_ext = \DB::connection('CRM_DWH');
        $coordinate = $db_ext->table('SPR_WAREHOUSE')
        ->select('GUID','WAREHOUSE_NAME','PRE_NAME','LONGITUDE','LATITUDE')
        ->get();
        foreach($coordinate as $c) {
            $add=$c->PRE_NAME;
            $name=$c->GUID;
            $conv=urlencode($add);
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://maps.google.com/maps/api/geocode/json?address='.$conv.'&key=AIzaSyA86O2e55O4nvcr342va66R2PXJYxBVjXo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $res = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($res, true);
            if(empty($res['results'])){
                $dataltd='NULL';
                $datalng='NULL';
            }
            else{
                $coor = $res['results'][0]['geometry']['location'];
                $datalng=$coor['lng'];
                $dataltd=$coor['lat'];
            }
                $koor=$db_ext->table('SPR_WAREHOUSE')
                ->where('GUID', '=', $name)
                ->update([
                    'LONGITUDE' => $datalng,
                    'LATITUDE' =>$dataltd
                ]);     
         $log  = "NAME: ".$name.' - '.date("F j, Y, g:i a").PHP_EOL.
        "Attempt: ".$datalng." - ".$dataltd." ".PHP_EOL.
        "-------------------------".PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents('/var/www/crm/log_WAREHOUSE_COORDINATES_'.date("j.n.Y").'.log', $log, FILE_APPEND);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function coord()
    {
        $db_ext = \DB::connection('CRM_DWH');
        $coordinates = $db_ext->table('KOORDINATY_KONTRAGENTA')
        ->select('IIN_BIN','CLIENT_COORDINATE','FAKT_ADRESS','MOTHER_OFFICE')
        ->get();
        foreach($coordinates as $c) {
            $hom=str_replace(" ", '', $c->MOTHER_OFFICE);
            $add=str_replace(" ", '', $c->KOORDINATY);
            $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://router.project-osrm.org/route/v1/driving/overview=false',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        $res = json_decode($response, true);

        $db_ext->table('KOORDINATY_KONTRAGENTA')
            ->where('FAKT_ADRESS', '!=', $add)
                ->where('KOORDINATY', '!=', '')
            ->update([
                'distance' => $res['routes']['distance'],
                'weight' => $res['routes']['weight'],
            ]);
        return $res['routes'];
        }
    }

    public function store_coord()
    {
        $db_ext = \DB::connection('CRM_DWH');
        $coordinates = $db_ext->table('KOORDINATY_KONTRAGENTA')
        ->select('IIN_BIN','CLIENT_COORDINATE','FAKT_ADRESS','MOTHER_OFFICE')
        ->get();
        foreach($coordinates as $c) {
            $hom=str_replace(" ", '', $c->MOTHER_OFFICE);
            $add=str_replace(" ", '', $c->KOORDINATY);
            $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://router.project-osrm.org/route/v1/driving/;?overview=false',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        $res = json_decode($response, true);

        $db_ext->table('KOORDINATY_KONTRAGENTA')
            ->where('FAKT_ADRESS', '!=', $add)
                ->where('KOORDINATY', '!=', '')
            ->update([
                'distance' => $res['routes']['distance'],
                'weight' => $res['routes']['weight'],
            ]);
        return $res['routes'];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coordinates  $coordinates
     * @return \Illuminate\Http\Response
     */
    public function show(Coordinates $coordinates)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Coordinates  $coordinates
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coordinates $coordinates)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Coordinates  $coordinates
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coordinates $coordinates)
    {
        //
    }
    // protected function guard() {
    //     return Auth::guard();
    // }
}
