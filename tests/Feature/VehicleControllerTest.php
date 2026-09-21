<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class VehicleControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_a_paginated_list_of_only_the_authenticated_users_vehicles(): void
    {
        $user = User::factory()->create();
        $vehicles = Vehicle::factory()->count(5)->for($user)->create();
        Vehicle::factory()->for(User::factory()->create())->create();

        Passport::actingAs($user);

        $this->getJson('/api/vehicles?page=2&per_page=2')
            ->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('per_page', 2)
            ->assertJsonPath('last_page', 3)
            ->assertJsonPath('total', 5)
            ->assertJsonPath('data.0.id', $vehicles[2]->id)
            ->assertJsonPath('data.1.id', $vehicles[1]->id)
            ->assertJsonCount(2, 'data');
    }

    public function test_returns_401_when_listing_vehicles_without_authentication(): void
    {
        $this->getJson('/api/vehicles')->assertUnauthorized();
    }

    public function test_returns_422_when_per_page_is_greater_than_100(): void
    {
        Passport::actingAs(User::factory()->create());

        $this->getJson('/api/vehicles?per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    public function test_uses_default_per_page_when_parameter_is_empty(): void
    {
        Passport::actingAs(User::factory()->create());

        $this->getJson('/api/vehicles?per_page=')
            ->assertOk()
            ->assertJsonPath('per_page', 15);
    }
}
