<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB; 
class subcidesAll extends JsonResource
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
            "id" => (int)$this->YEAR,
            "season" => "Сезон ".(int)$this->YEAR,
            "categories" => $this->Data($this->YEAR)
        //    "providerName" => $this->PROVIDER_NAME,
        //    "providerIin" => (int)$this->PROVIDER_IIN_BIN,
        //    "productName" => $this->PRODUCT,
        //    "sumSubcides" => $this->classificationSum($this->SUM_SUBSIDIES). " ₸",
        //    "productVolume" => (float)$this->VOLUME,
        //    "productUnit" => $this->UNIT,
        //    "usageArea" => (float)$this->USAGE_AREA. " Га"
        ];
    }
    private function Data($year){
        $data = DB::table("CRM_SHYMBULAK_SUBSIDIES")
        ->select("TYPE","YEAR",  DB::raw("ROW_NUMBER() OVER(ORDER BY TYPE ASC) AS idi"))
        ->where("YEAR", $year)
        ->where("CORRECT_APPLICANT_IIN_BIN", $this->CORRECT_APPLICANT_IIN_BIN)
        ->orderByDesc("YEAR")
        ->groupBy("TYPE", "YEAR")
        ->get();
        $collect = $data->map(function ($type){
            switch ($type->TYPE){
                case null:
                    $typeN = "Без категорий";
                    $typeC = null;
                    break;
                default:
                    $typeN = $type->TYPE;
                    $typeC = $type->TYPE;
            }
            
            $merge = collect([
                "id" => (int)$type->idi,
                "category" => $typeN,
                "contracts" => $this->List($type->YEAR, $typeC)
                ]);
                return $merge;
            
            }
        );
        return $collect;
    }
    private function List($year, $type){
        $data = DB::table("CRM_SHYMBULAK_SUBSIDIES")
        ->select("APPLICANT_NAME as clientName", "REGION as region", "USAGE_AREA as usageArea", "PROVIDER_NAME as providerName", "PRODUCT as productName", "SUM_SUBSIDIES as summary", "VOLUME as count", "UNIT as unit")
        ->where("YEAR", $year)
        ->where("TYPE", $type)
        ->where("CORRECT_APPLICANT_IIN_BIN", $this->CORRECT_APPLICANT_IIN_BIN)
        ->orderByDesc("YEAR")
        ->get();
        return $data->map(function ($datalist){
            return collect(
                [
                    "clientName" => $datalist->clientName,
                    "region" => $datalist->region,
                    "usageArea" => $datalist->usageArea." Га",
                    "providerName" => $datalist->providerName,
                    "productName" => $datalist->productName,
                    "productPrice" => number_format(round($datalist->summary) / round($datalist->count), 0, ".", " ")." ₸",
                    "sum" => $this->classificationSum($datalist->summary),
                    "count" => number_format(round($datalist->count), 0, ".", " "),
                    "unit" => $datalist->unit
                ]
        );
        });
        return $data;
    }

    public function classificationSum($number){
        $billions = number_format(floor($number / 1000000000), 0, '.', '');
        $millions = number_format(floor(($number % 1000000000) / 1000000), 0, '.', '');
        $thousands = number_format(($number % 1000000) / 1000, 0, '.', '');
        $parts = array($billions, $millions, $thousands);
        if($parts[0] == 0){
            return  $parts[1] . ' млн. ' . $parts[2] . ' тыс. ₸'; // Выведет "1 млрд. 234 млн. 567 тыс."
        }
        if($parts[1] == 0){
           return $parts[2] . ' тыс. ₸';
        }
       
    }
}
