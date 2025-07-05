<?php

namespace App\Notifications\TravelOrder;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelOrderStatusChangeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly TravelOrder $travelOrder)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your Travel Order has been {$this->travelOrder->status}")
            ->greeting("Hi, {$this->travelOrder->requestor_name}.")
            ->line("Your travel request status has been updated to: {$this->travelOrder->status}")
            ->line('Thank you for using our application!');
    }
}
