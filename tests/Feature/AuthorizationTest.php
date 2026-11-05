<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SeedsRolesAndPermissions;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/properties');

        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_forbidden(): void
    {
        $this->seedRoles();
        $technician = $this->makeUser('Technician');

        $response = $this->actingAs($technician)->get('/properties');

        $response->assertForbidden();
    }

    public function test_user_with_permission_can_view_properties(): void
    {
        $this->seedRoles();
        $owner = $this->makeUser('Owner');

        $response = $this->actingAs($owner)->get('/properties');

        $response->assertOk();
    }

    public function test_tenant_role_cannot_access_admin_dashboard(): void
    {
        $this->seedRoles();
        $tenant = $this->makeUser('Tenant');

        $response = $this->actingAs($tenant)->get('/dashboard');

        $response->assertRedirect(route('tenant.dashboard'));
    }

    public function test_tenant_role_cannot_access_global_search(): void
    {
        $this->seedRoles();
        $tenant = $this->makeUser('Tenant');

        $response = $this->actingAs($tenant)->get('/search?q=test');

        $response->assertForbidden();
    }

    public function test_finance_cannot_delete_property_without_permission(): void
    {
        $this->seedRoles();
        $finance = $this->makeUser('Finance');
        $property = Property::create([
            'name' => 'Kost Test', 'code' => 'KT01', 'address' => 'Jl. Test', 'status' => 'active',
        ]);

        $response = $this->actingAs($finance)->delete("/properties/{$property->id}");

        $response->assertForbidden();
    }
}
