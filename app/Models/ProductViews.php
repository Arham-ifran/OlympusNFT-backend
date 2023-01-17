<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductViews extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'product_views';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'ip_address',
        'is_view',
        'is_active',

    ];
    
 
    
}
