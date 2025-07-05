<?php

namespace Feature\TravelOrder;

use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\TravelOrder\TravelOrderStatusChangeNotification;
use App\Services\TravelOrderService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UpdateStatusTravelOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_user_can_accept_travel_order_successfully(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create();

        $response = $this->withToken($this->createTokenByUser($user))
            ->patchJson("api/travel-orders/{$travelOrder->id}/accept");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
            'message' => 'Travel Order accepted successfully!']);

        $this->assertDatabaseHas('travel_orders', [
            'id' => $travelOrder->id,
            'status' => 'accepted'
        ]);

        Notification::assertSentTo(
            $travelOrder->user,
            TravelOrderStatusChangeNotification::class,
            function ($notification, $channels) use ($travelOrder) {
                return $notification->travelOrder->id === $travelOrder->id
                    && in_array('mail', $channels);
            }
        );
    }

    public function test_user_can_cancel_travel_order_successfully(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create();

        $response = $this->withToken($this->createTokenByUser($user))
            ->patchJson("api/travel-orders/{$travelOrder->id}/cancel");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'message' => 'Travel Order canceled successfully!']);

        $this->assertDatabaseHas('travel_orders', [
            'id' => $travelOrder->id,
            'status' => 'canceled'
        ]);

        Notification::assertSentTo(
            $travelOrder->user,
            TravelOrderStatusChangeNotification::class,
            function ($notification, $channels) use ($travelOrder) {
                return $notification->travelOrder->id === $travelOrder->id
                    && in_array('mail', $channels);
            }
        );

    }

    public function test_guest_cannot_accept_travel_order_status(): void
    {
        $travelOrder = TravelOrder::factory()->create();

        $response = $this->patchJson("api/travel-orders/{$travelOrder->id}/accept");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJson([
            'error' => 'Unauthorized',
        ]);
    }

    public function test_guest_cannot_cancel_travel_order_status(): void
    {
        $travelOrder = TravelOrder::factory()->create();

        $response = $this->patchJson("api/travel-orders/{$travelOrder->id}/cancel");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJson([
            'error' => 'Unauthorized',
        ]);
    }

    public function test_cannot_accept_status_because_internal_error(): void
    {
        $user = User::factory()->create();
        $this->mock(TravelOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('updateStatus')
                ->once()
                ->andThrow(new \Exception('Internal Server Error!'));
        });

        $travelOrder = TravelOrder::factory()->create();

        $response = $this->withToken($this->createTokenByUser($user))
            ->patchJson("api/travel-orders/{$travelOrder->id}/accept");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['error' => 'Internal Server Error!']);
    }

    public function test_cannot_cancel_status_because_internal_error(): void
    {
        $user = User::factory()->create();
        $this->mock(TravelOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('updateStatus')
                ->once()
                ->andThrow(new \Exception('Internal Server Error!'));
        });

        $travelOrder = TravelOrder::factory()->create();

        $response = $this->withToken($this->createTokenByUser($user))
            ->patchJson("api/travel-orders/{$travelOrder->id}/cancel");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['error' => 'Internal Server Error!']);
    }

    public function test_cannot_accept_status_your_own_travel_order(): void {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create(['requestor_id' => $user->id]);

        $response = $this->withToken($this->createTokenByUser($user))
            ->patchJson("api/travel-orders/{$travelOrder->id}/accept");

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonFragment([
                'message' => 'You are not authorized to update the status of your own travel order.']);
    }
    public function test_cannot_cancel_status_your_own_travel_order(): void {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create(['requestor_id' => $user->id]);

        $response = $this->withToken($this->createTokenByUser($user))
            ->patchJson("api/travel-orders/{$travelOrder->id}/cancel");

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonFragment([
                'message' => 'You are not authorized to update the status of your own travel order.']);
    }

    public function test_try_cancel_status_after_departure_date(): void {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'departure_date' => '2025-01-01',
        ]);

        $response = $this->withToken($this->createTokenByUser($user))
            ->patchJson("api/travel-orders/{$travelOrder->id}/cancel");

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonFragment([
                'message' => 'A travel order cannot be canceled if the departure date has already passed.']);
    }

}
