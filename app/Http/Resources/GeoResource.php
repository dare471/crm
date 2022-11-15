<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeoResource extends JsonResource
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
            'owner'=>$this->owner,
            'fid'=>$this->fid,
            'cult'=>$this->cult,
            'region'=>$this->region,
            'district'=>$this->district,
            'area'=>$this->area,
            'year'=>$this->year,
            'kad_number'=>$this->kad_number,
            'title'=>$this->title,
            'geometry'=>json_decode($this->geometry),
	    'hasc_1'=>$this->hasc_1
            ];
    }
}
