<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategories extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'faq_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'is_active',

    ];

    
    // ************************** //
    //        Relationships       //
    // ************************** //

    public function faqs()
    {
        return $this->hasMany('App\Models\Faqs', 'category_id')->orderBy('id', 'ASC');
    }
}
