<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;
use Illuminate\Support\Str;

class Transactions extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     */
    protected $fillable = [
        'user_id',
        'transaction_of',
        'ad_id',
        'product_id',
        'order_id',
        'type',
        'from_address',
        'to_address',
        'transaction_hash',
        'paid_price',
        'earned_price',
        'transaction_status',
        'is_active',
        
    ];

    // ************************** //
    //        Relationships       //
    // ************************** //


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function ad()
    {
        return $this->belongsTo('App\Models\Ads', 'ad_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id', 'id');
    }
    public function order()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }
}
