<?php

namespace Feature\TravelOrder;

use App\Models\TravelOrder;
use App\Models\User;
use App\Services\TravelOrderService;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FindTravelOrderTest extends TestCase
{
    /* @var string */
    private const ENDPOINT = '/api/travel-orders/';

    public function test_can_find_travel_order_successfully(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create([
            'requestor_id' => $user->id,
        ]);

        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT . $travelOrder->id);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'data' => [
                    'id' => $travelOrder->id,
                    'requestor_name' => $travelOrder->requestor_name,
                    'departure_date' => $travelOrder->departure_date->format('Y-m-d'),
                    'return_date' => $travelOrder->return_date->format('Y-m-d'),
                    'status' => $travelOrder->status,
                    'destination' => $travelOrder->destination,
                    'requestor_id' => $travelOrder->requestor_id,
                    'created_at' => $travelOrder->created_at->format('Y-m-d H:i'),
                ]
            ]);
    }

    public function test_user_cannot_access_travel_order_of_another_user(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create();

        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT . $travelOrder->id);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                    'error' => 'Travel Order not found!'
            ]);
    }

    public function test_cannot_find_because_internal_error(): void
    {
        $user = User::factory()->create();
        $this->mock(TravelOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findById')
                ->once()
                ->andThrow(new \Exception('Internal Server Error!'));
        });

        $travelOrder = TravelOrder::factory()->create();

        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT . $travelOrder->id);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['error' => 'Internal Server Error!']);
    }
}
