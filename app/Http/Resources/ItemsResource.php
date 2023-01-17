<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
use Storage;

class ItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $auction_time = $this->auction_time;
       
        if ($this->auction_length_id) {
            if ($this->auction_time != "") {
                if (\Carbon\Carbon::now()->timestamp <= $this->auction_time)
                    $auction_time = $this->auction_time;
                else {
                    $auction_time = "Expired";
                }
            } else {
                $auction_time = "Expired";
            }
        }

        return [
            'id'                                => Hashids::encode($this->id),
            'store'                             => $this->store_id != "" ? $this->store->store_title : "OlympusNFT Store",
            'storeUrl'                          => $this->store_id != "" ? url('api/get-store-detail').'/'.Hashids::encode($this->store_id)  : "",
            'seller'                            => !empty($this->currentOwner->username) ? $this->currentOwner->username : "",
            'productTitle'                      =>  $this->title,
            'productSubTitle'                   => $this->sub_title,
            'productMedia'                      => $this->productMedia(),
            'ipfsImageHash'                     => $this->mainImageHash(),
            'transactionHash'                   => $this->transaction_hash,
            'priceType'                         => $this->price_type,
            'priceUsd'                          => number_format($this->price_usd, 2, '.', ''),
            'bidPriceUsd'                       => number_format($this->bid_price_usd, 2, '.', ''),
            'auctionLengthId'                   => $this->auction_length_id,
            'auctionTime'                       => $auction_time,
            'views'                             => !empty($this->view_count) && $this->view_count <> null ? $this->view_count : 0,
            'slug'                              => $this->slug,
            'isActive'                          => $this->is_active,

        ];
    }
}
