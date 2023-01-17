<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Templates extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_type',
        'email_type',
        'title',
        'is_default',
        'attachment',
        'subject',
        'content',
        'header',
        'footer',
        'is_active'
    ];
}
