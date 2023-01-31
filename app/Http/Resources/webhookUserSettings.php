<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB; 

class webhookUserSettings extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $arr=array();
        $query = DB::table("CRM_CLIENT_TO_VISIT")
        ->select("CLIENT_ID")
        ->where("USER_ID", $this->id)
        ->get();
        foreach($query as $q){
            array_push($arr, (int)$q->CLIENT_ID);
        }

        return [
            "active" =>(int)$this->activated,
            "subscribesRegion" => json_decode($this->region_belongs)->region,
            "unFollowClients" => json_decode($this->unfollowClient)->clientId,        
            "favoriteClients" => $arr,
            "access_availability"=>json_decode($this->access_availability),
        ];
    }
}
