<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\AuthService;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /* @var string */
    private const ENDPOINT = 'api/auth/login';

    public function test_login_with_successfully(): void
    {
        $user = User::factory()->create([
            'email'    => 'teste@example.com',
            'password' => 'senha123',
        ]);

        $credentials = base64_encode('teste@example.com:senha123');

        $response = $this->withHeaders([
            'Authorization' => "Basic $credentials",
        ])->postJson(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'data' => [
                'name',
                'access_token',
            ]
        ]);
    }

    public function test_login_with_incorrect_credentials(): void
    {
        $credentials = base64_encode('teste@exampleinvalid.com:senha123');

        $response = $this->withHeaders([
            'Authorization' => "Basic $credentials",
        ])->postJson(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJson([
            'message' => 'User and/or password incorrect',
        ]);
    }

    public function test_cannot_login_because_internal_error(): void
    {
        $this->mock(AuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('login')
                ->once()
                ->andThrow(new \Exception('Internal Server Error!'));
        });

        $user = User::factory()->create([
            'email'    => 'teste@example.com',
            'password' => bcrypt('senha123'),
        ]);

        $credentials = base64_encode('teste@example.com:senha123');

        $response = $this->withHeaders([
            'Authorization' => "Basic $credentials",
        ])->postJson(self::ENDPOINT, ['email' => $user->email, 'password' => 'senha123']);


        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['error' => 'Internal Server Error!']);
    }
}
