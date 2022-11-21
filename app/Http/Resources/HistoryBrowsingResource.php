<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryBrowsingResource extends JsonResource
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
            'id' => $this->ID,
            'userID' => $this->USER_ID,
            'region'=> $this->REGION,
            'mode' => $this->MODE,
            'district' => $this->DISTRICT,
            'clientFields' => $this->CLIENT_FIELDS,
        ];
    }
}
