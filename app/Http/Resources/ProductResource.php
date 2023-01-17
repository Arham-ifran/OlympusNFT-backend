<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class ProductResource extends JsonResource
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
            'user_id'                       =>  Hashids::encode($this->user_id),
            'token_id'                      =>  $this->token_id,
            'token_address'                 =>  $this->token_address,
            'category_id'                   =>  $this->category_id,
            'title'                         =>  $this->title,
            'sub_title'                     =>  $this->sub_title,
            'listing_tag'                   =>  $this->listing_tag,
            'store'                         =>  $this->store_id != "" ? $this->store->store_title : "OlympusNFT Store",
            'description'                   =>  $this->description,
            'transfer_copyright_when_purchased'                  =>  $this->transfer_copyright_when_purchased,
            'price_type'                    =>  $this->price_type,
            'price_usd'                     =>  number_format($this->price_usd, 2, '.', ''),
            'bid_price_usd'                 =>  number_format($this->bid_price_usd, 2, '.', ''),
            'auction_length_id'             =>  $this->auction_length_id,
            'auction_time'                  =>  $auction_time,
            'is_allow_buyer_to_resell'      =>  $this->is_allow_buyer_to_resell,
            'media_files'                   =>  MediaFileResource::collection($this->mediaFiles),
            'transactionHash'               =>  $this->transaction_hash,
            'slug'                          =>  $this->slug,
            'is_active'                     =>  $this->is_active,
            'royaltyPercentage'             =>  $this->royalty_percentage,
            'royaltyAddress'                =>  $this->royalty_address,
            'currentOwner'                  =>  $this->current_owner,
            'availableQuantity'                      =>  $this->available_quantity,
        ];
    }
}
