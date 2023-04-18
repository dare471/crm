<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PlannedMettingDetail;

class nomenClatureGroup extends JsonResource
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
            "id" => (int)substr($this->SEZON, 11, 10),
            "season" => $this->SEZON,
            "categories" => $this->Data($request),
        ];
    }
    private function Data($request){
        $clientId = $request->clientId;
        $data = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA as cds")
        ->select("KATEGORII_NOMENKLATURY_GROUP as name", "cci.ID as clientId", DB::raw("ROW_NUMBER() OVER(ORDER BY KATEGORII_NOMENKLATURY_GROUP ASC) AS idi"))
        ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "cds.DOGOVOR_GUID")
        ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
        ->leftJoin("L1.dbo.NOMENKLATURA as ln", "ln.GUID", "cds.NOMENKLATURA_GUID")
        ->where("cci.ID", $request->clientId)
        ->where("cd.SEZON", $this->SEZON)
        ->orderByDesc("SEZON")
        ->groupBy("KATEGORII_NOMENKLATURY_GROUP", "cci.ID", "SEZON")
        ->get();
        $collect = $data->map(function ($name){
            $merget = collect([
                "id" => (int)$name->idi,
                "category" =>$name->name,
                "contracts" => $this->List($name->name, $name->clientId)
            ]);
                return $merget;
            }
        );
        return $collect;
    }
    private function List($category, $clientId){
        $data = DB::table("CRM_DOGOVOR_SPETSIFIKATSIYA as cds")
        ->select("NOMENKLATURA as productName", DB::raw("avg(TSENA) as avgPrice"), DB::raw("SUM(KOLICHESTVO) as count"))
        ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "cds.DOGOVOR_GUID")
        ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
        ->leftJoin("L1.dbo.NOMENKLATURA as ln", "ln.GUID", "cds.NOMENKLATURA_GUID")
        ->where("cci.ID", $clientId)
        ->where("KATEGORII_NOMENKLATURY_GROUP", $category)
        ->where("cd.SEZON", $this->SEZON)
        ->groupBy("NOMENKLATURA")
        ->get();
        return $data->map(function ($datain){
            return collect(
                    [
                        "productName" => $datain->productName,
                        "avgPrice"=> number_format(round($datain->avgPrice), 0, '.', " ")." ₸",
                        "count" => number_format(round($datain->count), 0, '.', " "),
                    ]
                );
       });   
    }
}
