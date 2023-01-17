<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NewsletterSubscribers extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'newslettter_subscribers';
 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'status',
        
    ];

   
}
