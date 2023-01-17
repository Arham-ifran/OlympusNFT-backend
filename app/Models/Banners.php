<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Banners extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'banners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'title',
        'sub_title',
        'description',
        'link',
        'is_active',
        
    ];

    protected $appends = ['banner_img'];

  
    public function getBannerImgAttribute()
    {

        if ($this->image != "") {
            if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/banners/'.$this->id.'/' . $this->image)) {

                $image = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/banners/'.$this->id.'/'  . $this->image);
            } else {
                $image = asset('backend/images/no_img.jpg');
            }
        } else {
            $image = asset('backend/images/no_img.jpg');
        }
        return $this->attributes['banner_img'] = $image;
    }

}
