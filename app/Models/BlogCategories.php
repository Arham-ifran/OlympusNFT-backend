<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategories extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'blog_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'is_active',

    ];

    
    // ************************** //
    //        Relationships       //
    // ************************** //

    public function blogs()
    {
        return $this->hasMany('App\Models\Blogs', 'category_id')->orderBy('id', 'ASC');
    }
}
