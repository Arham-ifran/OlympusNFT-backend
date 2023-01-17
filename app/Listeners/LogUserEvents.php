<?php

namespace App\Listeners;

use App\Events\UserEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserEvents
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserEvents  $event
     * @return void
     */
    public function handle(UserEvents $event)
    {
        $user = auth($event->guard)->user();
        try {
            $log = new \App\Models\UserEvent();
            $log->user_id = $user->id;
            $log->is_admin = 1;
            $log->event_type_id = $event->event_type;
            $log->message = $event->message;
            $arr = ['id' => $user->id, 'firstname' => $user->firstname, 'lastname' => $user->lastname];
            if (!empty($event->meta) && is_array($event->meta)) {
                $arr = array_merge($arr, $event->meta);
            }
            $log->meta = $arr;
            $log->save();
        } catch (\Exception $ex) {
        }
    }
}
