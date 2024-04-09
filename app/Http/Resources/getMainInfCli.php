<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\getMainInfCliContacts;

class getMainInfCli extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $subquery = DB::table("CRM_CLIENT_CONTACTS")
        ->where("CLIENT_ID", $this->CLIENT_ID)
        ->get();
       
        return [
            "id" => (int)$this->ID,
            "address" => $this->ADDRESS,
            "name" => $this->NAME,
            "guid" => (boolean)$this->guid,
            "iinBin" => (int)$this->IIN_BIN,
            "cato" => (int)$this->CATO,
            "activity" => $this->DEYATELNOST,
            "district" => (int)$this->REGION,
            "region" => (int)$this->DISTRICT,
            "contacts" => getMainInfCliContacts::collection($subquery)->all(),
            "favorites" => $this->FavoritesStatus($request->userId),
        ];
    }
    private function FavoritesStatus($userId){
        $query = DB::table("CRM_CLIENT_TO_VISIT")
        ->where("USER_ID", $userId)
        ->where("CLIENT_ID", $this->ID)
        ->limit(1)
        ->get();
        return count($query) > 0; 
    }
}
