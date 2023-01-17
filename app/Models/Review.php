<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'reviews';

    /* The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rating',
        'review_title',
        'review',
        'is_active',
        'product_id',
        'order_id',
        'user_id',
        'seller_id'
    ];

   
    public function seller()
    {
        return $this->belongsTo('App\Models\User', 'seller_id');
    }
    public function reviewer_user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
    public function order()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id');
    }
    public function avg_rating()
    {
        return $this->avg('ratings');
    }
    
   

  
}
