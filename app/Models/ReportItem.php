<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportItem extends Model
{
    use HasFactory;
    protected $fillable = ['let_us_know_why', 'user_id', 'product_id', 'product_report_abuse_id'];

    public function products()
    {
        return $this->belongsTo('App\Models\Products', 'product_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function product_report_abuses()
    {
        return $this->belongsTo('App\Models\ProductReportAbuse', 'product_report_abuse_id', 'id');
    }
}
