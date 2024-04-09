<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
class ResourceForBaf extends JsonResource
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
            "id" => (int)$this->ID,
            "address" => (string)$this->ADDRESS,
            "name" => (string)$this->NAME,
            "bin" => (float)$this->IIN_BIN,
            "activity" => (string)$this->DEYATELNOST,
            "data" => [
                "firstContract" => $this->bafFirstContract($this->IIN_BIN), 
                "currentContract" => $this->bafCurrentContract($this->IIN_BIN), 
                "historicContract" => $this->historicContracts($this->IIN_BIN),
                "latestContract" => $this->latestContract($this->IIN_BIN),
                "twelveMonth" => $this->twelveMonth($this->IIN_BIN),
                "tweentyFourMonth" => $this->tweentyFourMonth($this->IIN_BIN)
            ],
        ];
    }
    private function tweentyFourMonth($iin){
        $query = DB::select("exec db_datareader.BAF_24M_CONTRACTS @IIN='$iin';");
        return $query;
    }
    private function twelveMonth($iin){
        $query = DB::select("exec db_datareader.baf_12m_contracts @IIN='$iin';");
        return $query;
    }
    private function latestContract($iin){
        $query = DB::select("exec db_datareader.BAF_LATEST_CONTRACT @IIN='$iin';");
        return $query;
    }
    private function historicContracts($iin){
        $query = DB::select("exec db_datareader.BAF_HISTORICAL_CONTRACT @IIN='$iin';");
        return $query;
    }
    private function bafFirstContract($iin){
        $query = DB::select("exec db_datareader.BAF_FIRST_CONTRACTS @IIN='$iin';");
        return $query;
    }
    private function bafCurrentContract($iin){
        $query = DB::select("EXEC db_datareader.BAF_CURRENT_CONTRACTS @IIN = '$iin'");
        return $query;
    }
    private function Object($clientId){
        return collect(["total_count"=>$this->currentContracts($clientId), ]);
    }
    private function currentContracts($id){
        $guidWithClient = $this-> getWithGuidIn($id);
        $query = DB::table("CRM_DOGOVOR")
        ->select("NAIMENOVANIE", "DATA")
        ->whereRaw("KONTRAGENT_GUID = CONVERT(binary(16), '$guidWithClient', 1)")
        ->count();
        return $query;
    }
    private function getWithGuidIn($id){
        $query = DB::table("CRM_CLIENT_ID_GUID")
       // ->select("ID", DB::raw("CONVERT(binary(16), 'GUID', 1)"))
        ->select(DB::raw("CONVERT(NVARCHAR(MAX), GUID, 1) as GUID"))
        ->where("ID",$id)
        ->value("GUID");
        return $query;
    }
}
