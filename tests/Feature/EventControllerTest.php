<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_event_with_only_the_authenticated_users_registered_vehicles(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $event = Event::factory()->for($user, 'organizer')->create();
        $vehicle = Vehicle::factory()->for($user)->create();
        $otherVehicle = Vehicle::factory()->for($otherUser)->create();

        $event->vehicles()->attach($vehicle, ['registration_date' => '2026-09-14']);
        $event->vehicles()->attach($otherVehicle, ['registration_date' => '2026-09-14']);
        Passport::actingAs($user);

        $this->getJson("/api/events/{$event->id}")
            ->assertOk()
            ->assertJsonPath('id', $event->id)
            ->assertJsonPath('vehicles.0.id', $vehicle->id)
            ->assertJsonCount(1, 'vehicles');
    }

    public function test_returns_401_when_showing_an_event_without_authentication(): void
    {
        $event = Event::factory()->for(User::factory(), 'organizer')->create();

        $this->getJson("/api/events/{$event->id}")->assertUnauthorized();
    }
}
