<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'url',
        'order_by',
        'is_active'
    ];



    /**
     *  Relationships       
     *
     * @var array
     */
    
    public function categoryStores()
    {
        return $this->hasMany('App\Models\Stores', 'category_id')->orderBy('id', 'ASC');
    }
    public function products()
    {
        return $this->hasMany('App\Models\Products', 'category_id')->orderBy('id', 'ASC');
    }
    

    
}
