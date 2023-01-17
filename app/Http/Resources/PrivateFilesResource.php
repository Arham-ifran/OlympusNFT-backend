<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
class PrivateFilesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    
    public function toArray($request)
    {
        $image_hash = json_decode($this->ipfs_image_hash,true); 
        if ($image_hash) {
          
            $hash =   config('constants.IPFS_URL')."/".$image_hash["hash"];
               
         } else {
             $hash = asset('backend/images/no_img.jpg');
        }

            return [
                'id'                    => $this->id,
                 'productMedia'         =>$this->ipfs_image_hash,
                'ipfsImageHash'         => $hash,
                
    
            ];
    
    }
}

