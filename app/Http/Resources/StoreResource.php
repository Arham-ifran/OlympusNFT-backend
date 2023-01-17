<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
class StoreResource extends JsonResource
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
            'user'                              => $this->user->username,
            'category'                          => $this->storeCategory->title!=""?$this->storeCategory->title:"",
            'store_title'                       => $this->store_title,
            'sub_title'                         => $this->sub_title,
            'total_items'                        => count($this->products),
            'image'                             => $this->storeImage(),
            'slug'                              => $this->slug,
           

        ];


    }
}
