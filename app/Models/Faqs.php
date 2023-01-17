<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Faqs extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'faqs';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'is_active'
    ];


    // ************************** //
    //        Relationships       //
    // ************************** //
    public function faqCategory()
    {
        return $this->belongsTo('App\Models\FaqCategories', 'category_id', 'id');
    }


    // ************************** //
    //  Extra Attributes           //
    // ************************** //
    
    public function get_date()
    {
        return Carbon::parse($this->created_at)->format('d M, Y G:i A');
    }
}
