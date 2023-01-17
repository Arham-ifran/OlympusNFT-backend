<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class HomePageBannerResource extends JsonResource
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
            'bannerId'         => Hashids::encode($this->id),
            'bannerImage'       => $this->banner_img,
            'bannerTitle'       => $this->title,
            'bannerSubTitle'       => $this->sub_title,
            'bannerDescription'       => $this->description,
        ];
    }
}
