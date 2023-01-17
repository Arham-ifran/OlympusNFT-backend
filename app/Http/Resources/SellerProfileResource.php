<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class SellerProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $array = new \stdClass();
        // $array = [
        //     'five'         => $this->rating(5),
        //     'four'         => $this->rating(4),
        //     'three'        => $this->rating(3),
        //     'two'          => $this->rating(2),
        //     'one'          => $this->rating(1),

        // ];
        $user = [
            'id'                              => Hashids::encode($this->id),
            'username'                        => $this->username,
            'fullName'                       => $this->firstname . ' ' . $this->lastname,
            'profilePhoto'                   => $this->profile_image,
            'bannerImage'                     => $this->banner_img,
            'totalItem'                       => count($this->products),
            'totalViewed'                     => $this->products->sum('view_count'),
            'totalReview'                     => count($this->seller_reviews),
            'totalRating'                     => $this->seller_reviews->sum('rating')
        ];

        return [
            'user'                            => $user,
            'stores'                          => StoreResource::collection($this->stores->take(10)),
        ];
    }
}
