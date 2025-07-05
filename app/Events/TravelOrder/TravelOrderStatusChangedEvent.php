<?php

namespace App\Events\TravelOrder;

use App\Models\TravelOrder;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TravelOrderStatusChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public TravelOrder $travelOrder)
    {
    }
}
