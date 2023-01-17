<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Messages extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'messages';
    protected $with = ['sender', 'receiver'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'thread_id',
        'message',
        'is_private',
        'is_read',
        'is_admin',
        'is_dispute'
    ];

    // ************************** //
    //        Relationships       //
    // ************************** //

    public function threads()
    {
        return $this->belongsTo('App\Models\MessageThreads', 'thread_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    // ************************** //
    //  Extra Attributes           //
    // ************************** //
    
    public function get_date()
    {
        return Carbon::parse($this->created_at)->format('d M, Y G:i A');
    }
}
