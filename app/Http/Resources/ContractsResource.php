<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'ContractGuid'=>$this->ContractGuid,
            'OrganizationName'=>$this->OrganizationName,
            'ManagerGuid'=>$this->ManagerGuid,
            'RegionGuid'=>$this->RegionGuid,
            'SeasonGuid'=>$this->SeasonGuid,
            'WarehouseGuid'=>$this->WarehouseGuid,
            'Currency'=>$this->Currency
            ];
    }
}
