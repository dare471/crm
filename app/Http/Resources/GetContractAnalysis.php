<?php

namespace App\Http\Resources;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

use function PHPSTORM_META\map;

class GetContractAnalysis extends JsonResource
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
            "id" => (int)substr($this->SEZON, 11, 15),
            "season" => $this->SEZON,
            "contracts" => $this->group($this->ID, $this->SEZON),
            "avgContracts"=> $this->avgContracts($this->ID)[0]
        ];
    }
    private function avgContracts($id){
        $query = DB::table("L1.dbo.ANALIZ_DOGOVORA as ad")
        ->select(DB::raw("avg(CAST(cd.SUMMA_KZ_TG as int)) as sum"), DB::raw("AVG(CAST(ad.MARZHINALNOST as int)) as margin"), "cd.SEZON as season")
        ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "ad.DOGOVOR_GUID")
        ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
        ->where("cci.ID", $id)
        ->where("cd.SEZON", $this->SEZON)
        ->groupBy("cd.SEZON")
        ->get();
        return $query->map(function ($item){
            return collect([
                "sum" => number_format($item->sum),
                "margin" => $item->margin,
                "season" => (int)substr($item->season, 11, 15)
            ]);
        });
    }
    private function group($id, $season){
        $query = DB::table("L1.dbo.ANALIZ_DOGOVORA as ad")
        ->select("cd.SUMMA_KZ_TG as sum", DB::raw("CAST(ROW_NUMBER() OVER(ORDER BY SUMMA_KZ_TG ASC) as int) AS id"), "ad.MARZHINALNOST as margin", "cd.SEZON as season", "cd.USLOVIYA_OPLATY as conditionPay")
        ->leftJoin("CRM_DOGOVOR as cd", "cd.GUID", "ad.DOGOVOR_GUID")
        ->leftJoin("CRM_CLIENT_ID_GUID as ccig", "ccig.GUID", "cd.KONTRAGENT_GUID")
        ->leftJoin("CRM_CLIENT_INFO as cci", "cci.CLIENT_ID", "ccig.ID")
        ->where("cci.ID", $id)
        ->where("cd.SEZON", $season)
        ->where("ad.DATA", DB::raw("(SELECT MAX(DATA)
        FROM [L1].[dbo].[ANALIZ_DOGOVORA]
        WHERE DOGOVOR_GUID = ad.DOGOVOR_GUID)"))
        ->get();
        return $query->map(function ($item){
            return collect([
                "id" => (int)$item->id,
                "margin" => number_format($item->margin, 2, '.', ''),
                "sum" => number_format($item->sum, 2, '.', ' '),
                "conditionPay" => $item->conditionPay
            ]);
        });
    }
}
