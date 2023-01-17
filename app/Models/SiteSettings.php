<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'site_settings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_logo',
        'site_name',
        'site_title',
        'site_keywords',
        'site_description',
        'launch_time',
        'site_email',
        'inquiry_email',
        'site_phone',
        'site_mobile',
        'site_address',
        'facebook',
        'twitter',
        'discord',
        'linkedin',
        'insta',
        'tiktok',
        'twitch',
        'youtube',
        'skype',
        'ad_manager_fee',
        'current_average_cpc_price',
        'suggested_cpc_price',
        'launch_time',
        'home_page_video',
    ];
}
