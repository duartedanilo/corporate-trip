<?php

namespace Tests\Feature;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TravelOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $token;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_admin' => true
        ]);
        $this->token = JWTAuth::claims(['is_admin' => $this->user->is_admin])->fromUser($this->user);
    }

    public function test_list_travel_orders(): void
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

    public function test_list_travel_orders_filtered_by_status(): void
    {
        TravelOrder::factory()->create([
            'requester' => $this->user,
            'status' => 0
        ]);

        TravelOrder::factory(3)->create([
            'requester' => $this->user,
            'status' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/travel-order?status=approved');

        $response->assertStatus(200)->assertJsonCount(3);
    }

    public function test_list_travel_orders_filtered_by_invalid_status(): void
    {
        TravelOrder::factory(3)->create([
            'requester' => $this->user,
            'status' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/travel-order?status=invalid');

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_list_travel_orders_filtered_by_destination(): void
    {
        TravelOrder::factory()->create([
            'requester' => $this->user,
            'destination' => 'Belém'
        ]);

        TravelOrder::factory(2)->create([
            'requester' => $this->user,
            'destination' => 'São Paulo'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/travel-order?destination=São Paulo');

        $response->assertStatus(200)->assertJsonCount(2);
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

    public function test_show_travel_order_by_id()
    {
        TravelOrder::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/travel-order/1');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'requester', 'destination', 'departure_date', 'return_date', 'status']);
    }

    public function test_show_non_existing_travel_order()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/travel-order/1');

        $response->assertStatus(404);
    }

    public function test_update_status()
    {
        TravelOrder::factory()->create([
            'requester' => User::factory()
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson('/api/travel-order/1/status', ['status' => 'approved']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('travel_order', ['id' => 1, 'status' => 1]);
    }

    public function test_update_travel_order_status_with_requester()
    {
        $travelOrder = TravelOrder::factory()->create([
            'requester' => User::factory()
        ]);
        $token = JWTAuth::fromUser($travelOrder->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/travel-order/1/status', ['status' => 'approved']);

        $response->assertStatus(403);
    }

    public function test_update_status_with_invalid_value()
    {
        TravelOrder::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson('/api/travel-order/1/status', ['status' => 'requested']);

        $response->assertStatus(422);
    }

    public function test_update_status_without_admin_user()
    {
        TravelOrder::factory()->create();

        $regularUser = User::factory()->create();
        $token = JWTAuth::claims(['is_admin' => $regularUser->is_admin])->fromUser($regularUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/travel-order/1/status', ['status' => 'requested']);

        $response->assertStatus(401);
    }
}
