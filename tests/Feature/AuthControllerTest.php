<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client', 'users');
    }

    public function test_registers_a_user_and_returns_a_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'senha-segura',
        ])
            ->assertCreated()
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token'])
            ->assertJsonPath('user.email', 'joao@example.com')
            ->assertJsonMissingPath('user.password_hash');

        $user = User::where('email', 'joao@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('senha-segura', $user->password_hash));
    }

    public function test_returns_422_when_registering_with_an_email_already_in_use(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $this->postJson('/api/register', [
            'name' => 'Outro João',
            'email' => 'joao@example.com',
            'password' => 'senha-segura',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_logs_in_with_valid_credentials_and_returns_a_token(): void
    {
        $user = User::factory()->create(['password_hash' => bcrypt('senha-correta')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'senha-correta',
        ])
            ->assertOk()
            ->assertJsonStructure(['user' => ['id', 'email'], 'token'])
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonMissingPath('user.password_hash');

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_returns_401_when_logging_in_with_a_wrong_password(): void
    {
        $user = User::factory()->create(['password_hash' => bcrypt('senha-correta')]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'senha-errada',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Credenciais inválidas.')
            ->assertJsonMissingPath('token');
    }
}
