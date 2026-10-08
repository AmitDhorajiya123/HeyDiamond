<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ClientRepository::class)->createPersonalAccessGrantClient('Feature test client', 'users');
    }

    public function test_login_issues_a_passport_token_and_logout_revokes_it(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $login->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', $user->email);

        $token = $login->json('access_token');
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->withToken($token)->getJson('/api/user')->assertUnauthorized();
    }

    public function test_only_active_admins_can_manage_resources(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user, ['*'], 'api');

        $this->getJson('/api/admin/shapes')->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        Passport::actingAs($admin, ['*'], 'api');

        $this->postJson('/api/admin/shapes', [
            'shape_code' => 'OVAL',
            'shape_name' => 'Oval',
        ])->assertCreated()
            ->assertJsonPath('data.shape_code', 'OVAL');

        $this->getJson('/api/admin/shapes?per_page=10')->assertOk();
    }
}