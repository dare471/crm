<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class specificationContracts extends JsonResource
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
            // "contractGuid" => $this->CONTRACT_GUID,
            // "contractName" => $this->NAIMENOVANIE,
            // "contractPeriod" => $this->PERIOD,
            "productGuid" => $this->PRODUCT_GUID,
            "productName" => $this->NOMENKLATURA,
            "typeCulture" => $this->VIDY_KULTUR,
            "productCount" =>  (int)$this->KOLICHESTVO,
            "productPrice" => (int)$this->TSENA,
            "productPriceDiscount" => (int)$this->TSENA_SO_SKIDKOY,
            "productPriceList" => (int)$this->TSENA_PO_PRAYS_LISTU,
            "productPriceMin" => (int)$this->TSENA_MIN,
            "productSum" => (int)$this->SUMMA,
            "productSumDiscount" => (int)$this->SUMMA_SO_SKIDKOY,
            "warehouseGuid"=> $this->WAREHOUSE_GUID,
            "warehouseName" => $this->SKLAD_OTGRUZKI,
         ];
    }
}
