<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Blogs extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'blogs';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'image',
        'slug',
        'is_active'
    ];


    // ************************** //
    //        Relationships       //
    // ************************** //
    public function blogCategory()
    {
        return $this->belongsTo('App\Models\BlogCategories', 'category_id', 'id');
    }


    // ************************** //
    //  Extra Attributes           //
    // ************************** //
    
    public function get_date()
    {
        return Carbon::parse($this->created_at)->format('d M, Y G:i A');
    }

  
}
