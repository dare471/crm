<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            "id" => $this->ID,
            "clientId" => $this->ELEMENT_ID,
            "description" => $this->DESCRIPTION,
            "createDate" => $this->CREATED_TIME,
            "updateDate" => $this->UPDATED_TIME,
            "createdBy" => $this->CREATED_BY,
            "category" => $this->CATEGORY_CHAPTER_ID
        ];
    }
}
