<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'currencies';
    protected $fillable = [
        'currency',
        'code',
        'is_active'
    ];

    
}
