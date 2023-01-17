<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdProducts extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'ad_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ad_id',
        'product_id',
        'is_active',

    ];
    
    public function ad()
    {
        return $this->belongsTo('App\Models\Ads', 'ad_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
    
}
