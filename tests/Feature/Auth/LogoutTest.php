<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\AuthService;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    /* @var string */
    private const ENDPOINT = 'api/auth/logout';

    public function test_logout_with_successfully(): void
    {
        $user = User::factory()->create([
            'email'    => 'teste@example.com',
            'password' => 'senha123',
        ]);

        $response = $this->withToken($this->createTokenByUser($user))->postJson(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertGuest();
    }

    public function test_logout_without_login(): void
    {
        $response = $this->postJson(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJson([
            'error' => 'Unauthorized',
        ]);
    }

    public function test_cannot_logout_internal_error(): void
    {
        $this->mock(AuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('logout')
                ->once()
                ->andThrow(new \Exception('Internal Server Error!'));
        });

        $user = User::factory()->create([
            'email'    => 'teste@example.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->withToken($this->createTokenByUser($user))->postJson(self::ENDPOINT);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['error' => 'Internal Server Error!']);
    }
}
