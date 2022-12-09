<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class FilterMaps extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $geo = explode(" | ", $this->geometry_rings);
       // $plotsid = explode(" | ", $this->plotId);
        $nnarray = [];
        // $narray = [];
        // $array = [];
        $countKeys = count($geo);
        for($i = 0; $i < $countKeys; $i++){
            $nnarray[$i] = json_decode($geo[$i]);
        }
        // for($i = 0; $i < $countKeys; $i++){
        //     $narray[$i] = json_decode($plotsid[$i]);
        // }
        // for($i = 0; $i < $countKeys; $i++){
        //     $array[] = array( "plotsId"=> $narray[$i], "geometry_rings"=> $nnarray[$i]);
        // }
    //    $serialize = json_decode($this->plots);
    //    $arr=[];
    //     foreach($serialize as $seria){
    //         $arr[]=[
    //             "plotsName" => $seria->plotsName,
    //             "plotsId" => $seria->plotsId,
    //             "geometry_ring" => json_decode($seria->geometry_ring)
    //         ];
    //     } 
        return [
            'type'=> $this->type,
            'regionId' => $this->region,
            'districtId' => $this->district,
            'clientId' => $this->clientID,
            'clientName' => $this->clientName,
            'clientBin' => $this->iin_bin,
            'cultureName' => $this->cultureName,
            'cultureId' => $this->cultureID,
            'color' => $this->color,
            'geometry_rings' => json_decode($this->geometry_rings)
        ];
    }
}
