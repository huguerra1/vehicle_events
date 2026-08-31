<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class EventVehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicleIds = Vehicle::pluck('id');

        Event::all()->each(function (Event $event) use ($vehicleIds) {
            $selected = $vehicleIds->random(min($vehicleIds->count(), rand(2, 5)));

            foreach ($selected as $vehicleId) {
                $event->vehicles()->attach($vehicleId, ['registration_date' => now()]);
            }
        });
    }
}
