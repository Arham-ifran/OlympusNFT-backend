<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class TransactionResource extends JsonResource
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
            'id'                      => Hashids::encode($this->id),
            'userName'                => $this->user->username,
            'type'                    => $this->type == 1 ? "Mint" : "Purchase",
            'fromAddress'             => $this->from_address,
            'toAddress'               => $this->to_address,
            'transactionHash'         => $this->transaction_hash,
            'transactionStatus'       => $this->transaction_status == 1 ? "Completed" : "Pending",
            'createdAt'               => Carbon::parse($this->created_at)->format('d M, Y')
        ];
    }
}
