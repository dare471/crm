<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getSubcides extends JsonResource
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
            "appNumber" => $this->APP_NUMBER,
            "applicantName" => $this->APPLICANT_NAME,
            "applicantBin" => (int)$this->APPLICANT_IIN_BIN,
            "providerName" => $this->PROVIDER_NAME,
            "providerBin" => (int)$this->PROVIDER_IIN_BIN,
            "appReceiverName" => $this->PROVIDER_IIN_BIN,
            "product" => $this->PRODUCT,
            "sum" => (int)$this->SUM_SUBSIDIES,
            "volume" => (int)$this->VOLUME,
            "unit" => $this->UNIT,
            "usageArea" => (int)$this->USAGE_AREA,
            "appType" => $this->APP_TYPE,
            "reProduction" => $this->REPRODUCTION,
            "paymentDate" => $this->PAYMENT_DATE,
            "address" => $this->ADDRESS,
            "activity" => $this->DEYATELNOST
        ];
    }
}
