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
    }
