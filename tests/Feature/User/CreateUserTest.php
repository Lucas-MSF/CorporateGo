<?php

namespace Tests\Feature\User;

use App\Services\UserService;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    /* @var string */
    private const ENDPOINT = 'api/auth/register';

    public function test_can_create_user_successfully(): void
    {
        $data = [
            'name'     => 'Lucas Macena',
            'email'    => 'lucas@email.com',
            'password' => 'hashed',
            'password_confirmation' => 'hashed',
        ];

        $response = $this->postJson(self::ENDPOINT, $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'User Created Successfully!'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
            'name'  => $data['name'],
        ]);
    }

    public function test_it_requires_required_fields_on_create(): void
    {
        $response = $this->postJson(self::ENDPOINT, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name' => 'The name field is required.',
                'email' => 'The email field is required.',
                'password' => 'The password field is required.',
            ]);
    }

    public function test_cannot_create_user_because_internal_error(): void
    {
        $data = [
            'name'     => 'Lucas Macena',
            'email'    => 'lucas@email.com',
            'password' => 'hashed',
            'password_confirmation' => 'hashed',
        ];

        $this->mock(UserService::class, function (MockInterface $mock) {
            $mock->shouldReceive('create')
                ->once()
                ->andThrow(new \Exception('Internal Server Error!'));
        });

        $response = $this->postJson(self::ENDPOINT, $data);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['error' => 'Internal Server Error!']);
    }
}
