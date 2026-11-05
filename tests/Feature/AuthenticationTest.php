<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\SeedsRolesAndPermissions;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_with_correct_credentials(): void
    {
        $this->seedRoles();
        $user = $this->makeUser('Owner', ['password' => Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_users_cannot_authenticate_with_incorrect_password(): void
    {
        $this->seedRoles();
        $user = $this->makeUser('Owner', ['password' => Hash::make('password')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $this->seedRoles();
        $user = $this->makeUser('Owner', ['password' => Hash::make('password'), 'status' => 'inactive']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_tenant_is_redirected_to_portal_after_login(): void
    {
        $this->seedRoles();
        $user = $this->makeUser('Tenant', ['password' => Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('tenant.dashboard'));
    }

    public function test_users_can_logout(): void
    {
        $this->seedRoles();
        $user = $this->makeUser('Owner');

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
