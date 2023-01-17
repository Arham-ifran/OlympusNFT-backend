<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReportAbuse extends Model
{
    use HasFactory;
    protected $fillable = ['title','description','short_desc','is_active'];

    // ************************** //
    //        Relationships       //
    // ************************** //
    public function report_items()
    {
        return $this->hasMany('App\Models\ReportItem', 'product_report_abuse_id');
    }
}
