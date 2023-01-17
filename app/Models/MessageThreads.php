<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageThreads extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'message_threads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'product_id',
        'max_message',
        'is_read'
    ];

    // ************************** //
    //        Relationships       //
    // ************************** //

    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id', 'id');
    }
    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'receiver_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Messages', 'thread_id')->orderBy('id', 'DESC');
    }

    
    
}
