<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;

class MessageThreadsResource extends JsonResource
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
            'id'                                => $this->id,
            'senderId'                          => Hashids::encode($this->sender_id),
            'senderUsername'                    => $this->sender->username,
            'senderProfileImage'                    => $this->sender->profile_image,
            'receiverId'                        => Hashids::encode($this->receiver_id),
            'receiverUsername'                  => $this->receiver->username,
            'receiverProfileImage'              => $this->receiver->profile_image,
            'productId'                        => $this->product_id,
            'lastMessage'                      => $this->messages->first()->message,


        ];
    }
}
