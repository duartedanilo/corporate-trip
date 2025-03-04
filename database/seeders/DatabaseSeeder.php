<?php

namespace Database\Seeders;

use App\Models\TravelOrder;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'is_admin' => 1
        ]);

        User::factory(3)->create()->each(function ($user) {
            TravelOrder::factory(2)->create(['requester' => $user->id]);
        });;
    }
}
