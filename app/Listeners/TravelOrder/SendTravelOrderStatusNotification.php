<?php

namespace App\Listeners\TravelOrder;

use App\Events\TravelOrder\TravelOrderStatusChangedEvent;
use App\Notifications\TravelOrder\TravelOrderStatusChangeNotification;
use Illuminate\Support\Facades\Notification;

class SendTravelOrderStatusNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TravelOrderStatusChangedEvent $event): void
    {
        $user = $event->travelOrder->user;
        $user->notify(new TravelOrderStatusChangeNotification($event->travelOrder));
    }
}
