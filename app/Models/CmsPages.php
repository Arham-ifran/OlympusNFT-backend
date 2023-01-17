<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPages extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'cms_pages';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'tracking_code',
        'seo_url',
        'sort_by',
        'meta_description',
        'meta_title',
        'meta_keywords',
        'show_in_header',
        'show_in_footer',
        'is_active'
    ];

    public static function boot()
    {
        parent::boot();
        static::created(function ($model) {
        });
        static::updated(function ($model) {
        });
        static::deleted(function ($model) {
        });
    }
}
