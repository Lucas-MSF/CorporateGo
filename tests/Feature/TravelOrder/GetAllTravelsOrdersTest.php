<?php

namespace Feature\TravelOrder;

use App\Enum\StatusTravelOrderEnum;
use App\Models\TravelOrder;
use App\Models\User;
use App\Services\TravelOrderService;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetAllTravelsOrdersTest extends TestCase
{
    /* @var string */
    private const ENDPOINT = '/api/travel-orders';

    public function test_can_get_all_travels_orders_successfully(): void
    {
        $user = User::factory()->create();
        TravelOrder::factory()->count(5)->create([
            'requestor_id' => $user->id,
        ]);

        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'requestor_name',
                        'departure_date',
                        'return_date',
                        'status',
                        'destination',
                        'requestor_id',
                        'created_at',
                    ],
                ]
            ])->assertJsonCount(5, 'data');
    }
    public function test_can_get_all_with_status_filter_travels_orders_successfully(): void
    {
        $user = User::factory()->create();
        TravelOrder::factory()->count(2)->create([
            'requestor_id' => $user->id,
        ]);

        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT . '?status_id='. StatusTravelOrderEnum::ACCEPTED->value);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => []
            ])->assertJsonCount(0, 'data');
    }

    public function test_can_get_all_with_destination_filter_travels_orders_successfully(): void
    {
        $user = User::factory()->create();
        TravelOrder::factory()->count(2)->create([
            'requestor_id' => $user->id,
        ]);

        TravelOrder::factory()->create([
            'requestor_id' => $user->id,
            'destination'  => 'Bahia'
        ]);

        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT . '?destination=Bahia');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' =>  [
                    [
                        'id',
                        'requestor_name',
                        'departure_date',
                        'return_date',
                        'status',
                        'destination',
                        'requestor_id',
                        'created_at',
                    ]
                ],
            ])->assertJsonCount(1, 'data');
    }

    public function test_can_get_all_with_start_and_end_date_filter_travels_orders_successfully(): void
    {
        $user = User::factory()->create();
        TravelOrder::factory()->count(2)->create([
            'requestor_id' => $user->id,
            'departure_date' => '2020-01-02'
        ]);

        TravelOrder::factory()->create([
            'requestor_id' => $user->id,
        ]);

        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT . '?start_date=2020-01-01&end_date=2020-01-03');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' =>  [
                    [
                        'id',
                        'requestor_name',
                        'departure_date',
                        'return_date',
                        'status',
                        'destination',
                        'requestor_id',
                        'created_at',
                    ]
                ],
            ])->assertJsonCount(2, 'data');
    }

    public function test_cannot_get_all_with_only_start_date_filter_travels_orders(): void
    {
        $user = User::factory()->create();
        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT . '?start_date=2020-01-01');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['end_date' => 'The end date field is required when start date is present.']);
    }


    public function test_user_cannot_access_travel_order_of_another_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                    'data' => []
            ]);
    }

    public function test_cannot_find_because_internal_error(): void
    {
        $user = User::factory()->create();
        $this->mock(TravelOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAll')
                ->once()
                ->andThrow(new \Exception('Internal Server Error!'));
        });


        $response = $this->withToken($this->createTokenByUser($user))
            ->getJson(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['error' => 'Internal Server Error!']);
    }
}
