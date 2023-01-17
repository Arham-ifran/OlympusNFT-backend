<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class ReviewResource extends JsonResource
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
            "id" => $this->id,
            "reviewTitle" => $this->review_title,
            "review" => $this->review,
            "rating" => $this->rating,
            "createdAt" => Carbon::parse($this->created_at)->format('d M, Y'),
            "order_id"=>Hashids::encode($this->order->id),
            "product" => [
                "id" => Hashids::encode($this->product->id),
                "title" => $this->product->title,
                "slug" => $this->product->slug,
                "subTitle" => $this->product->sub_title,
                "available_quantity" => $this->product->available_quantity,
                'productMedia' => $this->product->productMedia(),
                "ipfsImageHash" => $this->product->mainImageHash(),
            ],

            "reviewerUser" => [
                "username" => !empty($this->reviewer_user->username)?$this->reviewer_user->username:"",
                "profileImage" => !empty($this->reviewer_user->profile_image)?$this->reviewer_user->profile_image:asset('backend/images/no_img.jpg') ,
            ]
        ];
    }
}
