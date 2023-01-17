<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class StoreDetailResource extends JsonResource
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
            'owner'                             => $this->user->username,
            'category'                          => $this->storeCategory->title != "" ? $this->storeCategory->title : "",
            'storeTitle'                        => $this->store_title,
            'subTitle'                          => $this->sub_title,
            'totalItems'                        => count($this->products),
            'storeImage'                        => $this->storeImage(),
            'description'                       => $this->description,
            'totalReview'                       => 2,
            'items'                             => ItemsResource::collection($this->products),
            'slug'                              => $this->slug,

        ];
    }
}
