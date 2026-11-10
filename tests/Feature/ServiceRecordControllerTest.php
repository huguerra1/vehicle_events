<?php

namespace Tests\Feature;

use App\Enums\ServiceType;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ServiceRecordControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_lists_only_the_service_records_of_the_given_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user)->create();
        $otherVehicle = Vehicle::factory()->for($user)->create();
        ServiceRecord::factory()->count(2)->for($vehicle)->create();
        ServiceRecord::factory()->for($otherVehicle)->create();

        Passport::actingAs($user);

        $this->getJson("/api/vehicles/{$vehicle->id}/service-records")
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.vehicle_id', $vehicle->id)
            ->assertJsonPath('data.1.vehicle_id', $vehicle->id);
    }

    public function test_creates_a_service_record_for_an_own_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user)->create();

        Passport::actingAs($user);

        $this->postJson("/api/vehicles/{$vehicle->id}/service-records", [
            'service_type' => ServiceType::TrocaOleo->value,
            'description' => 'Troca de óleo e filtro',
            'service_date' => '2026-10-01',
            'cost' => 250.5,
        ])
            ->assertCreated()
            ->assertJsonPath('vehicle_id', $vehicle->id)
            ->assertJsonPath('service_type', ServiceType::TrocaOleo->value);

        $this->assertDatabaseHas('service_records', [
            'vehicle_id' => $vehicle->id,
            'service_type' => ServiceType::TrocaOleo->value,
            'description' => 'Troca de óleo e filtro',
        ]);
    }

    public function test_shows_a_service_record_of_an_own_vehicle(): void
    {
        $user = User::factory()->create();
        $serviceRecord = ServiceRecord::factory()
            ->for(Vehicle::factory()->for($user))
            ->create();

        Passport::actingAs($user);

        $this->getJson("/api/service-records/{$serviceRecord->id}")
            ->assertOk()
            ->assertJsonPath('id', $serviceRecord->id)
            ->assertJsonPath('vehicle_id', $serviceRecord->vehicle_id);
    }

    public function test_updates_a_service_record_of_an_own_vehicle(): void
    {
        $user = User::factory()->create();
        $serviceRecord = ServiceRecord::factory()
            ->for(Vehicle::factory()->for($user))
            ->create(['description' => 'Descrição antiga']);

        Passport::actingAs($user);

        $this->putJson("/api/service-records/{$serviceRecord->id}", [
            'description' => 'Descrição nova',
        ])
            ->assertOk()
            ->assertJsonPath('id', $serviceRecord->id)
            ->assertJsonPath('description', 'Descrição nova');

        $this->assertDatabaseHas('service_records', [
            'id' => $serviceRecord->id,
            'description' => 'Descrição nova',
        ]);
    }

    public function test_deletes_a_service_record_of_an_own_vehicle(): void
    {
        $user = User::factory()->create();
        $serviceRecord = ServiceRecord::factory()
            ->for(Vehicle::factory()->for($user))
            ->create();

        Passport::actingAs($user);

        $this->deleteJson("/api/service-records/{$serviceRecord->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('service_records', ['id' => $serviceRecord->id]);
    }

    public function test_returns_404_when_listing_service_records_of_another_users_vehicle(): void
    {
        $otherVehicle = Vehicle::factory()->for(User::factory())->create();
        ServiceRecord::factory()->for($otherVehicle)->create();

        Passport::actingAs(User::factory()->create());

        $this->getJson("/api/vehicles/{$otherVehicle->id}/service-records")
            ->assertNotFound()
            ->assertJsonMissingPath('data');
    }

    public function test_returns_404_when_creating_a_service_record_for_another_users_vehicle(): void
    {
        $otherVehicle = Vehicle::factory()->for(User::factory())->create();

        Passport::actingAs(User::factory()->create());

        $this->postJson("/api/vehicles/{$otherVehicle->id}/service-records", [
            'service_type' => ServiceType::Revisao->value,
            'service_date' => '2026-10-01',
            'cost' => 100,
        ])->assertNotFound();

        $this->assertDatabaseCount('service_records', 0);
    }

    public function test_returns_404_when_showing_a_service_record_of_another_users_vehicle(): void
    {
        $serviceRecord = ServiceRecord::factory()
            ->for(Vehicle::factory()->for(User::factory()))
            ->create();

        Passport::actingAs(User::factory()->create());

        $this->getJson("/api/service-records/{$serviceRecord->id}")
            ->assertNotFound()
            ->assertJsonMissingPath('id');
    }

    public function test_returns_404_when_updating_a_service_record_of_another_users_vehicle(): void
    {
        $serviceRecord = ServiceRecord::factory()
            ->for(Vehicle::factory()->for(User::factory()))
            ->create(['description' => 'Descrição original']);

        Passport::actingAs(User::factory()->create());

        $this->putJson("/api/service-records/{$serviceRecord->id}", [
            'description' => 'Descrição alterada',
        ])->assertNotFound();

        $this->assertDatabaseHas('service_records', [
            'id' => $serviceRecord->id,
            'description' => 'Descrição original',
        ]);
    }

    public function test_returns_404_when_deleting_a_service_record_of_another_users_vehicle(): void
    {
        $serviceRecord = ServiceRecord::factory()
            ->for(Vehicle::factory()->for(User::factory()))
            ->create();

        Passport::actingAs(User::factory()->create());

        $this->deleteJson("/api/service-records/{$serviceRecord->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('service_records', ['id' => $serviceRecord->id]);
    }

    public function test_returns_422_when_creating_a_service_record_without_service_type(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user)->create();

        Passport::actingAs($user);

        $this->postJson("/api/vehicles/{$vehicle->id}/service-records", [
            'service_date' => '2026-10-01',
            'cost' => 100,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('service_type');

        $this->assertDatabaseCount('service_records', 0);
    }

    public function test_returns_401_when_listing_service_records_without_authentication(): void
    {
        $vehicle = Vehicle::factory()->for(User::factory())->create();

        $this->getJson("/api/vehicles/{$vehicle->id}/service-records")->assertUnauthorized();
    }
}
