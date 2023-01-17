<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class ProductMediaFiles extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'product_media_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'is_private_file',
        'name',
        'ipfs_image_hash',
        'ipfs_json_file_hash',
        'is_token_image',
        'is_active',
    ];


    // ************************** //
    //        Relationships       //
    // ************************** //
    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id', 'id');
    }


    // **********************************************//
    //   Custom function for get media file path     //
    // **********************************************//

    public function mediafile()
    {

        if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/products/' . $this->product->id . '/preview-files/' . $this->name)) {

            $media_file = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/products/' . $this->product->id . '/preview-files/' . $this->name);
        } else {
            $media_file = asset('backend/images/no_img.jpg');
        }

        return  $media_file;
    }
}
