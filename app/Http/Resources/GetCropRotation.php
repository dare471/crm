<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class GetCropRotation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request){
        return [
            "id" => (int)$this->YEAR,
            "season" => $this->YEAR,
            "cultures" => $this->groupCulture($request)
        ];
    }
    private function groupCulture($request){
        $query = DB::table("CRM_SHYMBULAK_PIVOT_AREA_STRUCTURE as cspas")
        ->select( DB::raw("CAST(ROW_NUMBER() OVER(ORDER BY CULTURE ASC) as int) AS id"),"cspas.CULTURE as culture", DB::raw("CONCAT(AREA, ' ', 'Га') as area"))
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.IIN_BIN", "cspas.OWNER_IIN_BIN")
        ->where("cci.ID", $request->clientId)
        ->where("cspas.YEAR", $this->YEAR)
        ->get();
        return $query;
    }
}
