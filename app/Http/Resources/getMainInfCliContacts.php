<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getMainInfCliContacts extends JsonResource
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
            "position" => $this->POSITION,
            "clientId" => (int)$this->CLIENT_ID,
            "name" => $this->NAME,
            "phoneNumber" => (int)$this->PHONE_NUMBER,
            "email" => $this->EMAIL,
            "author" => $this->AUTHOR_ID,
            "updateTime" => $this->UPDATE_TIME,
            "actual" => $this->ACTUAL
        ];
    }
}
