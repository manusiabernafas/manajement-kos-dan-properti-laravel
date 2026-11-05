<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesPropertyStructure;
use Tests\Support\SeedsRolesAndPermissions;
use Tests\TestCase;

class TenantManagementTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;
    use CreatesPropertyStructure;

    public function test_staff_can_create_tenant_with_auto_generated_code(): void
    {
        $this->seedRoles();
        $staff = $this->makeUser('Staff');

        $response = $this->actingAs($staff)->post('/tenants', [
            'full_name' => 'Siti Nurhaliza', 'phone' => '081234567890', 'gender' => 'female', 'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', ['full_name' => 'Siti Nurhaliza', 'tenant_code' => 'TNT-00001']);
    }

    public function test_tenant_codes_increment_sequentially(): void
    {
        $this->seedRoles();
        $staff = $this->makeUser('Staff');

        $this->actingAs($staff)->post('/tenants', ['full_name' => 'Tenant Satu', 'phone' => '0811', 'gender' => 'male', 'status' => 'active']);
        $this->actingAs($staff)->post('/tenants', ['full_name' => 'Tenant Dua', 'phone' => '0812', 'gender' => 'male', 'status' => 'active']);

        $this->assertDatabaseHas('tenants', ['full_name' => 'Tenant Satu', 'tenant_code' => 'TNT-00001']);
        $this->assertDatabaseHas('tenants', ['full_name' => 'Tenant Dua', 'tenant_code' => 'TNT-00002']);
    }

    public function test_nik_must_be_unique(): void
    {
        $this->seedRoles();
        $staff = $this->makeUser('Staff');

        $this->actingAs($staff)->post('/tenants', [
            'full_name' => 'Tenant A', 'nik' => '3201010101010001', 'phone' => '0811', 'gender' => 'male', 'status' => 'active',
        ]);

        $response = $this->actingAs($staff)->post('/tenants', [
            'full_name' => 'Tenant B', 'nik' => '3201010101010001', 'phone' => '0812', 'gender' => 'male', 'status' => 'active',
        ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_tenant_with_transactions_cannot_be_deleted(): void
    {
        $this->seedRoles();
        $owner = $this->makeUser('Owner');
        $room = $this->makeRoom();
        $tenant = \App\Models\Tenant::create([
            'tenant_code' => 'TNT-99999', 'full_name' => 'Tenant Lama', 'phone' => '0811', 'gender' => 'male', 'status' => 'active',
        ]);
        $tenant->invoices()->create([
            'invoice_number' => 'INV-99999', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7), 'total' => 100000, 'remaining_amount' => 100000, 'status' => 'unpaid',
        ]);

        $response = $this->actingAs($owner)->delete("/tenants/{$tenant->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', ['id' => $tenant->id, 'deleted_at' => null]);
    }
}
