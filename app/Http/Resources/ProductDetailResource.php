<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
use App\Models\Products;
use App\Models\ProductReportAbuse;
use Carbon\Carbon;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    protected $is_viewed;

    public function __construct($resource, $is_viewed)
    {

        parent::__construct($resource);
        $this->resource = $resource;
        $this->is_viewed = $is_viewed; // $apple param passed
    }

    public function toArray($request)
    {


        $report_abuses = ProductReportAbuse::select('id', 'title', 'description', 'is_active')->where('is_active', 1)->get();
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

        if ($this->bids->where('is_winner_bid', 1)->first()) {
            $Won_bid = $this->bids->where('is_winner_bid', 1)->first();
            $won_bid_id = $Won_bid->id;
            $won_bid_user_id = $Won_bid->bidder_id;
            $won_bid_price = $Won_bid->price;
        }

        return [
            'user_id' => Hashids::encode($this->user_id),
            'seller' => $this->currentOwner->username,
            'categoryId' => $this->category_id,
            'category' => $this->category->title,
            'title' => $this->title,
            'subTitle' => $this->sub_title,
            'rating'  => $this->avgRating() == null ? 0 : $this->avgRating(),
            'listingTag' => $this->listing_tag,
            'store' => $this->store_id != "" ? $this->store->store_title : "OlympusNFT Store",
            'downloadableFile' => $this->downloadable_file,
            'description' => $this->description,
            'transferCopyrightWhenPurchased' => $this->transfer_copyright_when_purchased,
            'priceType' => $this->price_type,
            'priceUsd' => number_format($this->price_usd, 2, '.', ''),
            'bidPriceUsd' => !empty($this->last_bid_price->price) ? number_format($this->last_bid_price->price, 2, '.', '') : number_format($this->bid_price_usd, 2, '.', ''),
            'auctionLengthId' => $this->auction_length_id,
            'auctionTime' =>  $auction_time,
            'total_bid' => count($this->bids),
            'minimum_bid' =>  !empty($this->last_bid_price->price) ? $this->last_bid_price->price : $this->bid_price_usd,
            'last_bid' =>  !empty($this->last_bid_price->price) ? $this->last_bid_price->price : 0,
            'lastBidUser' =>  !empty($this->last_bid_price->bidder->username) ? $this->last_bid_price->bidder->username : "",
            'wonBidId' =>  !empty($won_bid_id) ? Hashids::encode($won_bid_id) : "",
            'wonBidUserId' =>  !empty($won_bid_user_id) ? Hashids::encode($won_bid_user_id) : "",
            'wonBidPrice' =>  !empty($won_bid_price) ? $won_bid_price : "",
            'OrderId' => !empty($this->order->id) ? Hashids::encode($this->order->id) : "",
            'isSold' => $this->is_sold,
            'twitter' => $this->currentOwner->twitter,
            'instagram' => $this->currentOwner->instagram,
            'youtube' => $this->currentOwner->youtube,
            'facebook' => $this->currentOwner->facebook,
            'isAllowBuyerToResell' => $this->is_allow_buyer_to_resell,
            'contractAddress' => $this->contract_address,
            'tokenId' => $this->token_id,
            'tokenName' => $this->token_name,
            'tokenAddress' => $this->token_address,
            'tokenMetadata' => $this->token_metadata,
            'tokenOrignalImage' => $this->original_image,
            'tokenOrignalCreator' => $this->original_creator,
            'slug'        => $this->slug,
            'mediaFiles' => MediaFileResource::collection($this->mediaFiles),
            'transactionHash' => $this->transaction_hash,
            'isViewed'        => $this->is_viewed,
            'originalCreator' => $this->original_creator,
            'currentOwner' => $this->current_owner,
            'availableQuantity' => $this->available_quantity,
            'quantity' => $this->quantity,
            'isRelistedProduct' => $this->is_relisted_product,
            'royaltyPercentage'  => $this->royalty_percentage,
            'royaltyAddress'     => $this->royalty_address,
            'related_product' => ProductListingResource::collection($this->relatedProducts()),
            'reportAbuses' => $report_abuses,
            'createdAt'  => Carbon::parse($this->created_at)->format('d M, Y'),

        ];
    }
}
