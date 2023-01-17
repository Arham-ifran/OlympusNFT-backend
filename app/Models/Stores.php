<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Stores extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'stores';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'store_title',
        'sub_title',
        'store_tags',
        'image',
        'description',
        'store_your_data',
        'royalty_amount',
        'increase_batch_minting',
        'slug',
        'is_active',

    ];

    // ************************** //
    //        Relationships       //
    // ************************** //
    public function storeCategory()
    {
        return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Products', 'store_id');
    }



    // *************************************************//
    //   Custom function for get Store Image path //
    // ************************************************//

    public function storeImage()
    {
        if ($this->image != "") {
            if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/stores/' . $this->id . '/' . $this->image)) {

                $image = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/stores/' . $this->id . '/' . $this->image);
            } else {
                $image = asset('backend/images/no_img.jpg');
            }
        } else {
            $image = asset('backend/images/no_img.jpg');
        }
        return  $image;
    }
}
