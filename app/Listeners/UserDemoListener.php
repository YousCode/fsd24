<?php

namespace App\Listeners;

use App\Events\UserDemoEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserDemoListener
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
     * @param  UserDemoEvent  $event
     * @return void
     */
    public function handle(UserDemoEvent $event)
    {
        
    }
}
