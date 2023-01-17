<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;
use Illuminate\Support\Str;

class Products extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     */
    protected $fillable = [
        'user_id',
        'store_id',
        'category_id',
        'auction_length_id',
        'title',
        'sub_title',
        'listing_tag',
        'description',
        'transfer_copyright_when_purchased	',
        'price_type',
        'price_usd',
        'bid_price_usd',
        'auction_time',
        'token_id',
        'token_name',
        'token_address',
        'token_metadata',
        'contract_address',
        'original_image',
        'original_creator',
        'is_allow_buyer_to_resell',
        'is_private_files',
        'downloadable_file',
        'view_count',
        'slug',
        'transaction_hash',
        'is_sold',
        'is_relisted_product',
        'is_active',
        'royalty_percentage',
        'royalty_address',
        'current_owner',
        'quantity',
        'available_quantity',
        'parent_product_id'
    ];

    // ************************** //
    //        Relationships       //
    // ************************** //

    public function ads()
    {
        return $this->belongsToMany('App\Models\Ads', 'ad_products', 'ad_id', 'product_id');
    }

    public function mediaFiles()
    {
        return $this->hasMany('App\Models\ProductMediaFiles', 'product_id')->where('is_private_file', 0)->orderBy('id', 'ASC');
    }
    public function privateFiles()
    {
        return $this->hasMany('App\Models\ProductMediaFiles', 'product_id')->where('is_private_file', 1)->orderBy('id', 'ASC');
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function currentOwner()
    {
        return $this->belongsTo('App\Models\User', 'current_owner', 'wallet_address');
    }


    public function store()
    {
        return $this->belongsTo('App\Models\Stores', 'store_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
    }

    public function auctionLength()
    {
        return $this->belongsTo('App\Models\AuctionLength', 'auction_length_id', 'id');
    }

    public function report_items()
    {
        return $this->hasMany('App\Models\ReportItem', 'product_id');
    }


    public function reviews()
    {
        return $this->hasMany('App\Models\Review', 'product_id');
    }


    public function bids()
    {
        return $this->hasMany('App\Models\Bids', 'product_id')->orderby("price", "DESC");
    }

    public function last_bid_price()
    {

        return $this->hasOne('App\Models\Bids', 'product_id')->latest();
    }

    public function highest_bid()
    {

        return $this->hasOne('App\Models\Bids', 'product_id')->orderby("price", "DESC");
    }

    // public function orders()
    // {
    //     return $this->hasMany('App\Models\Order', 'product_id');
    // }
    public function order()
    {
        return $this->hasOne('App\Models\Orders', 'product_id');
    }


    public function investors()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function artists()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function musicians()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    // ************************** //
    //  Append Extra Attributes   //
    // ************************** //


    public function setTitleAttribute($value)
    {

        if (static::whereSlug($slug = Str::slug($value))->exists()) {

            $slug = $this->incrementSlug($slug);
        } else {
            $slug = Str::slug($value);
        }

        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($slug);
    }


    // *******************//
    // Custom functions  //
    // ******************//

    /**
     * INCREAMENT SLUG IN CASE IF SLUG ALREADY EXIST
     *
     */
    public function incrementSlug($slug)
    {

        $original = $slug;

        $count = 2;

        while (static::whereSlug($slug)->exists()) {

            $slug = "{$original}-" . $count++;
        }

        return $slug;
    }
    /**
     * GET MEDIA FILE
     *
     */


    public function mainImageHash()
    {

        if ($this->mediaFiles->where('is_token_image', 1)->first()) {
            $main_image_hash_json = $this->mediaFiles->where('is_token_image', 1)->first()->ipfs_image_hash;
            $main_image_hash = json_decode($main_image_hash_json, true);
            if ($main_image_hash) {

                $hash =   config('constants.IPFS_URL') . "/" . $main_image_hash["hash"];
            } else {
                $hash = asset('backend/images/no_img.jpg');
            }
        } else {
            $hash = asset('backend/images/no_img.jpg');
        }

        return  $hash;
    }

    public function productMedia()
    {
        $product_media = "";
        if ($this->mediaFiles->where('is_token_image', 1)->first())
            $product_media  = $this->mediaFiles->where('is_token_image', 1)->first()->ipfs_image_hash;
        else {
            $product_media  = "";
        }

        return  $product_media;
    }

    // public function scopeMediafile()
    // {

    //     if ($this->mediaFiles->where('is_token_image', 1)->first()) {
    //         if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/products/' . $this->id . '/preview-files/' . $this->mediaFiles->where('is_token_image', 1)->first()->name)) {

    //             $media_file = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/products/' . $this->id . '/preview-files/' . $this->mediaFiles->where('is_token_image', 1)->first()->name);
    //         } else {
    //             $media_file = asset('backend/images/no_img.jpg');
    //         }
    //     } else {
    //         $media_file = asset('backend/images/no_img.jpg');
    //     }

    //     return  $media_file;
    // }

    public function avgRating()
    {
        return $this->reviews->where('product_id', $this->id)->avg('rating');
    }

    public function relatedProducts()
    {
        $tags = explode(',', $this->listing_tag);
        $related_products = self::where('parent_product_id', '!=', $this->parent_product_id);
        $related_products->where(function ($query) use ($tags) {
            $query->where('category_id', '=', $this->category_id);

            $query->orwhere(function ($q) use ($tags) {
                foreach ($tags as $key =>  $tag) {
                    if ($key == 0)
                        $q = $q->whereRaw('FIND_IN_SET(?, listing_tag)', $tag);
                    else
                        $q = $q->orwhereRaw('FIND_IN_SET(?, listing_tag)', $tag);
                }
            });
        });
        $related_products = $related_products->groupBy('parent_product_id')->limit(config('constants.DEFAULT_LIMIT'))->get();
        return  $related_products;
    }
}
