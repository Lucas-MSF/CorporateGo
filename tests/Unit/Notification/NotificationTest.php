<?php

namespace Tests\Unit\Notification;

use App\Models\TravelOrder;
use App\Notifications\TravelOrder\TravelOrderStatusChangeNotification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function test_travel_order_notification_renders_correct_email(): void
    {
        $travelOrder = TravelOrder::factory()->create([
            'status' => 'accepted',
        ]);

        $notification = new TravelOrderStatusChangeNotification($travelOrder);

        $mail = $notification->toMail($travelOrder->user);

        $this->assertStringContainsString('Your Travel Order has been accepted', $mail->subject);
        $this->assertStringContainsString($travelOrder->requestor_name, $mail->greeting);
    }
}
