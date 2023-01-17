<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoGuides extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'video_guides';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'url',
        'is_active',
        
    ];

  

}
