<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehiclesSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function (User $user) {
            Vehicle::factory()
                ->count(rand(1, 3))
                ->create(['user_id' => $user->id]);
        });
    }
}
