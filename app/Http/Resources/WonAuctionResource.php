<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
use Storage;

class WonAuctionResource extends JsonResource
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
            'orderId'                           => Hashids::encode($this->id),
            'productId'                         => Hashids::encode($this->product->id),
            'productTitle'                      => $this->product->title,
            'productMedia'                      => $this->product->productMedia(),
            'ipfsImageHash'                     => $this->product->mainImageHash(),
            'transactionHash'                   => $this->product->transaction_hash,
            'tokenId'                           => $this->product->token_id,
            'buyer'                             => $this->buyer->username,
            'seller'                            => $this->seller->username,
            'priceUsd'                          => number_format($this->price_usd, 2, '.', ''),
            'bidAmount'                         => $this->total,
            'totalBids'                         => $this->product->bids()->count(),
            'timeLeft'                          => $this->product->auction_time,
            'productslug'                       => $this->product->slug,
            'bidStatus'                         => $this->order_status->title,
            'privateFiles'                      => MediaFileResource::collection($this->product->privateFiles),
            'createdAt'                         => Carbon::parse($this->created_at)->format('d M, Y'),

        ];
    }
}
