<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'server_name'=>$this->server_name,
            'name'=>$this->name,
            'last_name'=>$this->last_name,
            'email'=>$this->email,
            'gender'=>$this->gender,
            'server_name'=>$this->server_name,
            'name'=>$this->name,
            'birth_date'=>$this->birth_date,
            'age'=>$this->age,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'status'=>$this->status,
            'experience'=>$this->experience,
            'orgstr_id'=>$this->orgstr_id,
            'region_id'=>$this->region_id,
            'devision_id'=>$this->devision_id,
            'access_id'=>$this->access_id
        ];
    }
}
