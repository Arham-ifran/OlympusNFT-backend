<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;
use Illuminate\Support\Str;

class Orders extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'seller_id',
        'buyer_id',
        'price_usd',
        'price_eth',
        'total',
        'is_auction_product',
        'bid_id',
        'bid_price_usd',
        'bid_price_eth',
        'transaction_hash',
        'from_address',
        'to_address',
        'order_status_id',
        'is_active',

    ];

    // ************************** //
    //        Relationships       //
    // ************************** //


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\User', 'seller_id', 'id');
    }
    public function buyer()
    {
        return $this->belongsTo('App\Models\User', 'buyer_id', 'id');
    }

    public function bid()
    {
        return $this->belongsTo('App\Models\Bids', 'bid_id', 'id');
    }

    public function order_status()
    {
        return $this->belongsTo('App\Models\OrderStatus', 'order_status_id', 'id');
    }

   


}
