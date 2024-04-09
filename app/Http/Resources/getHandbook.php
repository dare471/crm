<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class getHandbook extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($request->action == "getHandBookWorkDone"){
            return [
                "id" => (int)$this->id,
                "name" => $this->name,
            ];
        }
        if($request->action == "getHandBookWorkDone"){
            return [
                "id" => (int)$this->id,
                "name" => $this->name,
            ];
        }
        if($request->action == "getHandBookContractComplications"){
            return [
                "id" => (int)$this->id,
                "name" => $this->name,
            ];
        }
        if($request->action == "getHandBookMeetingRecommendations"){
            return [
                "id" => (int)$this->id,
                "name" => $this->name,
            ];
        }
        else{
            return [
                "id" => (int)$this->linkId,
                "name" => $this->name,
                "url" => $this->url,
                "category" => $this->categoryName,
                "categoryId" => (int)$this->categoryId
            ];
        }
    }
}
