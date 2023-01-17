<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

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
            'id'                                => Hashids::encode($this->id),
            'username'                          => $this->username,
            'profile_image'                     => $this->profile_image,
            
          
        ];
    }
}
