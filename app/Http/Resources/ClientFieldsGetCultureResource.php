<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientFieldsGetCultureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type' => 'clientLandPlot',
            'id' => $this->ID,
            'fields' => $this->FIELDS,
            'cultureID' => $this->CULTURE,
            'ciltureName'=> $this->NAME,
            'color' => $this->color,
            'client_info_id' => $this->CLIENT_INFO_ID,
            'guid'  => $this->guid,
            'geometry_rings' =>  json_decode($this->COORDINATES, true),
        ];
    }
}
