<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id');

        collect(range(1, 5))->each(function () use ($userIds) {
            Event::factory()->create([
                'organizer_id' => $userIds->random(),
            ]);
        });
    }
}
