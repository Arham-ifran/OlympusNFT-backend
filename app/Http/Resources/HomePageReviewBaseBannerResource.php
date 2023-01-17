<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class HomePageReviewBaseBannerResource extends JsonResource
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
            'sellerId'         => Hashids::encode($this->seller->id),
            'sellerName'       => $this->seller->username,
            'profileImage'     => $this->seller->profile_image,
            'avgRating'        => $this->average_rating,
            'userType'          => $this->seller->user_type,


        ];
    }
}
