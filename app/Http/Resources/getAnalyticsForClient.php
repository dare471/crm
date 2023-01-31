<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getAnalyticsForClient extends JsonResource
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
            "sumSubsClient" => (int)$this->sumSubsClient,
            "foreignMarkSumSubcides" => (int)$this->difSumSubcides,
            "percentSubs" => round($this->sumSubsClient/$this->difSumSubcides * 100, 3),
            "year" => (int)$this->YEAR,
            "productName" => $this->SEEDS_NAME,
            "region" => (int)$this->REGION,
            "sumVolumeClient" => (int)$this->sumVolumeClient,
            "foreignMarkSumVolume" => (int)$this->difSumVolume,
            "percentVolume" => $this->sumVolumeClient/$this->difSumVolume * 100,
            "culture" =>  $this->CULTURE
        ];
    }
}
