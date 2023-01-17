<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEvent extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'user_events';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'json',
    ];

    protected $fillable = [
        'user_id',
        'is_admin',
        'event_type_id',
        'message',
        'meta'
    ];

    public function eventType()
    {
        return $this->hasOne(EventType::class, 'id', 'event_type_id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'user_id');
    }
}
