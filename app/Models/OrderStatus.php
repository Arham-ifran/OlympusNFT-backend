<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;
use Illuminate\Support\Str;

class OrderStatus extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'order_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     */
    protected $fillable = [
        'id',
        'title',
        'is_active',

    ];

    // ************************** //
    //        Relationships       //
    // ************************** //
    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'order_status_id');
    }




}
