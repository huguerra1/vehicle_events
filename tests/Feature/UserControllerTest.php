<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_updates_the_authenticated_user(): void
    {
        $user = User::factory()->create(['name' => 'Nome Antigo']);

        Passport::actingAs($user);

        $this->putJson("/api/users/{$user->id}", ['name' => 'Nome Novo'])
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', 'Nome Novo');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nome Novo']);
    }

    public function test_returns_404_when_updating_another_user(): void
    {
        $otherUser = User::factory()->create(['name' => 'Outro Usuário']);

        Passport::actingAs(User::factory()->create());

        $this->putJson("/api/users/{$otherUser->id}", ['name' => 'Nome Invadido'])
            ->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $otherUser->id, 'name' => 'Outro Usuário']);
    }

    public function test_returns_404_when_deleting_another_user(): void
    {
        $otherUser = User::factory()->create();

        Passport::actingAs(User::factory()->create());

        $this->deleteJson("/api/users/{$otherUser->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
    }

    public function test_user_listing_does_not_expose_password_fields(): void
    {
        $user = User::factory()->create();
        User::factory()->create();

        Passport::actingAs($user);

        $response = $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonCount(2);

        foreach ($response->json() as $listedUser) {
            $this->assertArrayHasKey('email', $listedUser);
            $this->assertArrayNotHasKey('password_hash', $listedUser);
            $this->assertArrayNotHasKey('password', $listedUser);
            $this->assertArrayNotHasKey('remember_token', $listedUser);
        }
    }

    public function test_user_detail_does_not_expose_password_fields(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user);

        $this->getJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonMissingPath('password_hash')
            ->assertJsonMissingPath('password')
            ->assertJsonMissingPath('remember_token');
    }

    public function test_returns_401_when_listing_users_without_authentication(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();
    }
}
