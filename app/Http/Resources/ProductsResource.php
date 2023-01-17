<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
use Storage;

class ProductsResource extends JsonResource
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
            'category'                          => $this->category->title,
            'categoryDescription'               => $this->category->description,
            'resellable'                        => $this->is_allow_buyer_to_resell,
            'transferCopyright'                 => $this->transfer_copyright_when_purchased,
            'title'                             => $this->title,
            'subTitle'                          => $this->sub_title,
            'store'                             => $this->store_id != "" ? $this->store->store_title : "OlympusNFT Store",
            'productMedia'                      => $this->productMedia(),
            'ipfsImageHash'                     => $this->mainImageHash(),
            'transactionHash'                   => $this->transaction_hash,
            'priceType'                         => $this->price_type,
            'priceUsd'                          => number_format($this->price_usd, 2, '.', ''),
            'TokenId'                           => $this->token_id,
            'bidPriceUsd'                       => !empty($this->last_bid_price->price) ?number_format($this->last_bid_price->price, 2, '.', '') : number_format($this->bid_price_usd, 2, '.', ''),
            'auctionLengthId'                   => $this->auction_length_id,
            'auctionTime'                       => $auction_time,
            'slug'                              => $this->slug,
            'is_active'                         => $this->is_active,
            'royaltyPercentage'                 => $this->royalty_percentage,
            'royaltyAddress'                    => $this->royalty_address,
            'currentOwner'                      => $this->current_owner,
            'availableQuantity'                          => $this->available_quantity,
        ];
    }
}
