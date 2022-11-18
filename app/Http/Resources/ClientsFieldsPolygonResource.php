<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientsFieldsPolygonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      $serialize = explode(" | ", $this->geometry_rings);
      $nnarray = [];
        foreach($serialize as $cult){
            $json = json_decode($cult,true);
           // array_push($nnarray,$json[0]);
            $nnarray[] = $json[0];
        }
        return [
            'type' => 'clientFieldCulture',
            'nameCult' => $this->nameCult,
            'fieldsCultureId' => $this->fieldsCultureId,
            'client_info_id' =>  $this->client_info_id,
            'color' => $this->color,
            'geometry_rings' => $nnarray,
        ];
    }
}
