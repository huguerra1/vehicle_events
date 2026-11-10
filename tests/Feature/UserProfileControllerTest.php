<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_creates_the_authenticated_users_profile(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user);

        $this->postJson('/api/profile', [
            'bio' => 'Apaixonado por carros antigos',
            'instagram_handle' => '@carros_antigos',
            'phone' => '(11) 91234-5678',
        ])
            ->assertCreated()
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('instagram_handle', '@carros_antigos');

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'bio' => 'Apaixonado por carros antigos',
        ]);
    }

    public function test_returns_409_when_creating_a_profile_that_already_exists(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->for($user)->create();

        Passport::actingAs($user);

        $this->postJson('/api/profile', ['bio' => 'Outro perfil'])
            ->assertConflict();

        $this->assertDatabaseCount('user_profiles', 1);
    }

    public function test_shows_only_the_authenticated_users_profile(): void
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->for($user)->create();
        UserProfile::factory()->for(User::factory())->create();

        Passport::actingAs($user);

        $this->getJson('/api/profile')
            ->assertOk()
            ->assertJsonPath('id', $profile->id)
            ->assertJsonPath('user_id', $user->id);
    }

    public function test_returns_404_when_showing_a_profile_that_does_not_exist(): void
    {
        UserProfile::factory()->for(User::factory())->create();

        Passport::actingAs(User::factory()->create());

        $this->getJson('/api/profile')->assertNotFound();
    }

    public function test_updates_only_the_authenticated_users_profile(): void
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->for($user)->create(['bio' => 'Bio antiga']);
        $otherProfile = UserProfile::factory()->for(User::factory())->create(['bio' => 'Bio de outro usuário']);

        Passport::actingAs($user);

        $this->putJson('/api/profile', ['bio' => 'Bio nova'])
            ->assertOk()
            ->assertJsonPath('id', $profile->id)
            ->assertJsonPath('bio', 'Bio nova');

        $this->assertDatabaseHas('user_profiles', ['id' => $profile->id, 'bio' => 'Bio nova']);
        $this->assertDatabaseHas('user_profiles', ['id' => $otherProfile->id, 'bio' => 'Bio de outro usuário']);
    }

    public function test_returns_401_when_showing_the_profile_without_authentication(): void
    {
        $this->getJson('/api/profile')->assertUnauthorized();
    }

    public function test_returns_401_when_creating_a_profile_without_authentication(): void
    {
        $this->postJson('/api/profile', ['bio' => 'Sem token'])->assertUnauthorized();

        $this->assertDatabaseCount('user_profiles', 0);
    }
}
