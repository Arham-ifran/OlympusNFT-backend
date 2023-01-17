<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bids extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'bids';

    /* The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'seller_id',
        'bidder_id',
        'is_winner_bid',
        'price',
        'is_active',
    ];


    public function bidder()
    {
        return $this->belongsTo('App\Models\User', 'bidder_id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'bid_id');
    }

    public function total_bid_on_product()
    {
        return   self::where('product_id',  $this->product_id)->count();
    }
  }
