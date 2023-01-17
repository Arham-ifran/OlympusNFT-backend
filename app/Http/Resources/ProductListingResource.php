<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class ProductListingResource extends JsonResource
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
            'seller'                            => !empty($this->currentOwner->username) ? $this->currentOwner->username : "",
            'title'                             => $this->title,
            'sub_title'                         => $this->sub_title,
            'views'                             => !empty($this->view_count) && $this->view_count <> null ? $this->view_count : 0,
            'store'                             => $this->store_id != "" ? $this->store->store_title : "OlympusNFT store",
            'priceType'                         => $this->price_type,
            'priceUsd'                          => number_format($this->price_usd, 2, '.', ''),
            'bidPriceUsd'                       => number_format($this->bid_price_usd, 2, '.', ''),
            'auctionLengthId'                   => $this->auction_length_id,
            'auctionTime'                       => $auction_time,
            'productMedia'                      => $this->productMedia(),
            'ipfsImageHash'                     => $this->mainImageHash(),
            'transactionHash'                   => $this->transaction_hash,
            'originalCreator'                   => $this->original_creator,
            'tokenId'                           => $this->token_id,
            'slug'                              => $this->slug,
            'is_active'                         => $this->is_active,
            'royaltyPercentage'                 => $this->royalty_percentage,
            'royaltyAddress'                    => $this->royalty_address,
            'currentOwner'                      => $this->current_owner,
            'availableQuantity'                 => $this->available_quantity,
            'quantity'                          => $this->quantity,
            'createdAt'                         => Carbon::parse($this->created_at)->format('d M, Y'),    

        ];
    }
}
