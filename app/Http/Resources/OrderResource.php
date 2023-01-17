<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
use Storage;

class OrderResource extends JsonResource
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
            'availableQuantity'                          => $this->product->available_quantity,
            'transactionHash'                   => $this->transaction_hash,
            'tokenId'                           => $this->product->token_id,
            'privateFiles'                      => PrivateFilesResource::collection($this->product->privateFiles),
            'buyer'                             => $this->buyer->username,
            'seller'                            => $this->seller->username,
            'priceUsd'                          => number_format($this->price_usd, 2, '.', ''),
            'total'                             => $this->total,
            'productslug'                       => $this->product->slug,
            'status'                            => $this->order_status->title,
            "createdAt"                         => Carbon::parse($this->created_at)->format('d M, Y'),

        ];
    }
}
