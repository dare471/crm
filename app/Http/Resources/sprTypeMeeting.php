<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class sprTypeMeeting extends JsonResource
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
            "name" => $this->NAME
        ];
    }
}