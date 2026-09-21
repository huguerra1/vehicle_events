<?php

namespace Database\Seeders;

use App\Models\ServiceRecord;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class ServiceRecordsSeeder extends Seeder
{
    public function run(): void
    {
        Vehicle::all()->each(function (Vehicle $vehicle) {
            ServiceRecord::factory()
                ->count(rand(3, 10))
                ->create(['vehicle_id' => $vehicle->id]);
        });
    }
}
