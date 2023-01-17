<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
use Storage;

class MyEarningResource extends JsonResource
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
            "weekDay"                         => Carbon::parse($this->created)->format('l'),
            'sales'                           => $this->total_sales,
           

        ];
    }
}
