<?php

namespace App\Events;

use App\Models\User;
use App\Models\Admin;
use App\Models\Messages;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * User that sent the message
     *
     * @var User
     */
    public $user;
    public $admin;
    /**
     * Message details
     *
     * @var Message
     */
    public $message;

    public $channel;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user = null, Messages $message, $channel, $admin = null)
    {
        $this->user = $user;
        $this->admin = $admin;
        $this->message = $message;
        $this->channel = $channel;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return [$this->channel];
    }

    public function broadcastAs()
    {
        return 'message-sent';
    }
}
