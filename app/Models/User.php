<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Storage;
use Hashids;
use DB;
use App\Models\Review;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type',
        'username',
        'firstname',
        'lastname',
        'email',
        'password',
        'mobile',
        'role',
        'photo',
        'banner_image',
        'address',
        'address2',
        'country',
        'state',
        'city',
        'zipcode',
        'dob',
        'wallet_address',
        'twitter',
        'instagram',
        'about',
        'reedit',
        'cent',
        'youtube',
        'facebook',
        'email_notification',
        'last_login_on',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // ************************** //
    //        Relationships       //
    // ************************** //
    public function products()
    {
        return $this->hasMany('App\Models\Products', 'user_id')->orderby('id','Desc');
    }

    public function bids()
    {
        return $this->hasMany('App\Models\Bids', 'bidder_id');
    }

    public function ads()
    {
        return $this->hasMany('App\Models\Ads', 'user_id')->orderby('id','Desc');
    }

    public function stores()
    {
        return $this->hasMany('App\Models\Stores', 'user_id')->orderby('id','Desc');
    }


    public function seller_reviews()
    {
        return $this->hasMany('App\Models\Review', 'seller_id');
    }


    public function report_items()
    {
        return $this->hasMany('App\Models\ReportItem', 'user_id');
    }

    public function sender_message_threads()
    {
        return $this->hasMany('App\Models\MessageThreads', 'sender_id');
    }

    public function receiver_message_threads()
    {
        return $this->hasMany('App\Models\MessageThreads', 'receiver_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'seller_id');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Order', 'buyer_id');
    }
    public function transactions()
    {
        return $this->hasMany('App\Models\Transactions', 'user_id')->orderby('id','Desc');
    }

    public function investors_products()
    {
        return $this->hasMany('App\Models\Products', 'user_id');
    }
    public function artists_products()
    {
        return $this->hasMany('App\Models\Products', 'user_id');
    }
    public function musicians_products()
    {
        return $this->hasMany('App\Models\Products','user_id' );
    }


    // ************************** //
    //  Append Extra Attributes   //
    // ************************** //

    public function full_name()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    // public function photo()
    // {
    //     // $user = $this;
    //     // if ($user->user_type == 1) {
    //     //     return asset('frontend/dashboard/images/user-thumb-seller.png');
    //     // } else {
    //     //     return asset('frontend/dashboard/images/user-thumb-buyer.png');
    //     // }
    // }



    protected $appends = ['profile_image','banner_img'];

    public function setEmailNotificationAttribute($value)
    {

        $this->attributes['email_notification'] = $value == true ? 1 : 0;
    }

    // *************************************************//
    //   Custom function for get User profile path //
    // ************************************************//

    public function getProfileImageAttribute()
    {

        if ($this->photo != "") {
            if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/users/' . $this->photo)) {

                $image = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/users/' . $this->photo);
            } else {
                $image = asset('backend/images/no_user_img.jpg');
            }
        } else {
            $image = asset('backend/images/no_user_img.jpg');
        }
        return $this->attributes['profile_image'] = $image;
    }

    public function getBannerImgAttribute()
    {

        if ($this->banner_image != "") {
            if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/users/' . $this->banner_image)) {

                $image = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/users/' . $this->banner_image);
            } else {
                $image = asset('backend/images/no_img.jpg');
            }
        } else {
            $image = asset('backend/images/no_img.jpg');
        }
        return $this->attributes['banner_img'] = $image;
    }


    public function rating()
    {
        $ratings = Review::select('rating', DB::raw('count(id) as total_review'))
            ->groupBy('rating')
            ->orderby('rating', 'DESc')
            ->get();

        return  $ratings;
    }
}
