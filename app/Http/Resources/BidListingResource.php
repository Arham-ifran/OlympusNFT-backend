<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
use Carbon\Carbon;

class BidListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $auction_time = $this->product->auction_time;

        if ($this->product->auction_length_id) {
            if ($this->product->auction_time != "") {
                if (\Carbon\Carbon::now()->timestamp <= $this->product->auction_time)
                    $auction_time = $this->product->auction_time;
                else {
                    $auction_time = "Expired";
                }
            } else {
                $auction_time = "Expired";
            }
        }


        return [
            'id'                   => Hashids::encode($this->id),
            'productMedia'         => $this->product->productMedia(),
            'ipfsImageHash'        => $this->product->mainImageHash(),
            'productTitle'         => $this->product->title,
            'productSubTitle'      => $this->product->sub_title,
            'totalBids'            => $this->total_bid_on_product(),
            'bidAmount'            => number_format($this->price, 2, '.', ''),
            'timeLeft'             => $auction_time,
            'productId'            => Hashids::encode($this->product->id),
            'productSlug'          => $this->product->slug,
            'createdAt'             => Carbon::parse($this->created_at)->format('d M, Y')
        ];
    }
}
