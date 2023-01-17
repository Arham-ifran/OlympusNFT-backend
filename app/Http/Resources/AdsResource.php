<?php

namespace App\Http\Resources;
use Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class AdsResource extends JsonResource
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
            'id'   => Hashids::encode($this->id),
            'adTitle'      => $this->title,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'impression' => $this->impression,
            'bidType' => $this->bid_type,
            'cpc' => $this->cpc,
            'totalBudget' => $this->total_budget,
            'totalSpent' => $this->total_spent,
            'isActive'  => $this->is_active,
        ];
    }
}
