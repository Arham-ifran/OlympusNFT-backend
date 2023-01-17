<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class NftsListingResource extends JsonResource
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
            'id'                                => Hashids::encode($this->id),
            'productTitle'                      => $this->title,
            //'productMainImage'                => $this->scopeMediafile(),
            'productMedia'                      => $this->productMedia(),
            'ipfsImageHash'                     => $this->mainImageHash(),
            'productSubTitle'                   => $this->sub_title,
            'tokenId'                           => $this->token_id,
            'tokenAddress'                      => $this->token_address,
            'tokenMetadata'                     => $this->token_metadata,
            'contractAddress'                   => $this->contract_address,
            'transactionHash'                   => $this->transaction_hash,
            'slug'                              => $this->slug

        ];
    }
}
