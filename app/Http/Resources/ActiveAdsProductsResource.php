<?php

namespace App\Http\Resources;
use Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveAdsProductsResource extends JsonResource
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
            'adId'                              =>  Hashids::encode($this->ad_id),
            'id'                                =>  Hashids::encode($this->product->id),
            'seller'                            => !empty($this->product->currentOwner->username) ? $this->product->currentOwner->username : "",
            'category'                          => $this->product->category->title,
            'categoryDescription'               => $this->product->category->description,
            'resellable'                        => $this->product->is_allow_buyer_to_resell,
            'transferCopyright'                 => $this->product->transfer_copyright_when_purchased,
            'title'                             => $this->product->title,
            'subTitle'                          => $this->product->sub_title,
            'availableQuantity'                 => $this->product->available_quantity,
            'store'                             => $this->product->store_id != "" ? $this->product->store->store_title : "OlympusNFT Store",
            'productMedia'                      => $this->product->productMedia(),
            'ipfsImageHash'                     => $this->product->mainImageHash(),
            'transactionHash'                   => $this->product->transaction_hash,
            'priceType'                         => $this->product->price_type,
            'priceUsd'                          => number_format($this->product->price_usd, 2, '.', ''),
            'TokenId'                           => $this->product->token_id,
            'bidPriceUsd'                       => !empty($this->product->last_bid_price->price) ? $this->product->last_bid_price->price : $this->product->bid_price_usd,
            'auctionLengthId'                   => $this->product->auction_length_id,
            'auctionTime'                       => $auction_time,
            'slug'                              => $this->product->slug,
          
        ];
    }
}
