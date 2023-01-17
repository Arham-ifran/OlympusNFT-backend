<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'ads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'start_date',
        'end_date',
        'impression',
        'bid_type',
        'cpc',
        'total_budget',
        'total_spent',
        'is_active',

    ];

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Products', 'ad_products','ad_id','product_id');
    }
}
