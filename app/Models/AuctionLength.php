<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionLength extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'auction_lengths';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'is_active',

    ];

    public function products()
    {
        return $this->hasMany('App\Models\Products', 'auction_length_id')->orderBy('id', 'ASC');
    }
}
