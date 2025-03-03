<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_new_user(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password()
        ]);

        $response->assertStatus(201);
    }

    public function test_register_new_user_with_invalid_body(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => fake()->name(),
            'email' => fake()->email(),
        ]);

        $response->assertStatus(422);
    }

    public function test_login(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => fake()->email(),
            'password' => 'password'
        ]);

        $response->assertStatus(401);
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/logout');

        $response->assertStatus(200);
    }

    public function test_logout_without_token()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}
