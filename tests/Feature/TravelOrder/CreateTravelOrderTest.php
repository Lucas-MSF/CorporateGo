<?php

namespace Tests\Feature\TravelOrder;

use App\Models\User;
use App\Services\TravelOrderService;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateTravelOrderTest extends TestCase
{
    /** @var string */
    private const ENDPOINT = 'api/travel-orders';

    public function test_user_can_create_travel_order_successfully(): void
    {
        $user = User::factory()->create();
        $travelOrderData = [
            'requestor_name' => 'Lucas Macena',
            'destination'    => 'Belo Horizonte',
            'departure_date' => '2025-08-01',
            'return_date'    => '2025-08-10',
        ];

        $response = $this->withToken($this->createTokenByUser($user))
            ->postJson(self::ENDPOINT, $travelOrderData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
            'data' => [
                'id'                 => $response->json('data')['id'],
                'requestor_name'     => $travelOrderData['requestor_name'],
                'requestor_id'       => $user->id,
                'destination'        => $travelOrderData['destination'],
                'departure_date'     => $travelOrderData['departure_date'],
                'return_date'        => $travelOrderData['return_date'],
                'status'             => 'pending',
                'created_at'         => $response->json('data')['created_at'],
            ]
        ]);

        $this->assertDatabaseHas('travel_orders', $travelOrderData);
    }

    public function test_it_requires_required_fields_on_create(): void
    {
        $user = User::factory()->create();
        $response = $this->withToken($this->createTokenByUser($user))
            ->postJson(self::ENDPOINT, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'requestor_name'     => 'The requestor name field is required.',
                'destination'        => 'The destination field is required.',
                'departure_date'     => 'The departure date field is required.',
                'return_date'        => 'The return date field is required.',
        ]);
    }

    public function test_guest_cannot_create_travel_order(): void
    {
        $response = $this->postJson(self::ENDPOINT, []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJson([
            'error' => 'Unauthorized',
        ]);
    }

    public function test_cannot_create_because_internal_error(): void
    {
        $this->mock(TravelOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('create')
                ->once()
                ->andThrow(new \Exception('Internal Server Error!'));
        });

        $user = User::factory()->create();
        $travelOrderData = [
            'requestor_name' => 'Lucas Macena',
            'destination'    => 'Belo Horizonte',
            'departure_date' => '2025-08-01',
            'return_date'    => '2025-08-10',
        ];

        $response = $this->withToken($this->createTokenByUser($user))
            ->postJson(self::ENDPOINT, $travelOrderData);


        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['error' => 'Internal Server Error!']);
    }
}
