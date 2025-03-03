<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TravelOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_list_travel_orders_with_success(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/travel-order');

        $response->assertStatus(200);
    }

    public function test_list_travel_orders_without_token(): void
    {
        $response = $this->get('/api/travel-order');

        $response->assertStatus(401);
    }

    public function test_create_new_travel_order()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/travel-order', [
            'destination' => 'Belém',
            'departure_date' => '2025-03-02 22:00:00',
            'return_date' => '2025-03-10 22:00:00',
        ]);

        $response->assertStatus(201);
    }

    public function test_create_new_travel_order_without_required_fields()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/travel-order', [
            'destination' => 'Belém',
        ]);

        $response->assertStatus(422);
    }
}
