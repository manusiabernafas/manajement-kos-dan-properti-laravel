<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesPropertyStructure;
use Tests\Support\SeedsRolesAndPermissions;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;
    use CreatesPropertyStructure;

    protected function makeTenantWithUser(string $name): array
    {
        $user = User::factory()->create(['name' => $name, 'password' => Hash::make('password')]);
        $user->assignRole('Tenant');
        $tenant = $this->makeTenant(['full_name' => $name, 'user_id' => $user->id]);

        return [$user, $tenant];
    }

    public function test_tenant_can_only_see_their_own_invoices(): void
    {
        $this->seedRoles();
        $room = $this->makeRoom();
        [$userA, $tenantA] = $this->makeTenantWithUser('Tenant A');
        [$userB, $tenantB] = $this->makeTenantWithUser('Tenant B');

        $invoiceA = $tenantA->invoices()->create([
            'invoice_number' => 'INV-A001', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7), 'total' => 1000000, 'remaining_amount' => 1000000, 'status' => 'unpaid',
        ]);
        $invoiceB = $tenantB->invoices()->create([
            'invoice_number' => 'INV-B001', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7), 'total' => 2000000, 'remaining_amount' => 2000000, 'status' => 'unpaid',
        ]);

        $ownInvoiceResponse = $this->actingAs($userA)->get("/portal/invoices/{$invoiceA->id}");
        $ownInvoiceResponse->assertOk();

        $otherInvoiceResponse = $this->actingAs($userA)->get("/portal/invoices/{$invoiceB->id}");
        $otherInvoiceResponse->assertForbidden();
    }

    public function test_tenant_invoice_list_only_shows_own_invoices(): void
    {
        $this->seedRoles();
        $room = $this->makeRoom();
        [$userA, $tenantA] = $this->makeTenantWithUser('Tenant A');
        [$userB, $tenantB] = $this->makeTenantWithUser('Tenant B');

        $tenantA->invoices()->create([
            'invoice_number' => 'INV-A002', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7), 'total' => 1000000, 'remaining_amount' => 1000000, 'status' => 'unpaid',
        ]);
        $tenantB->invoices()->create([
            'invoice_number' => 'INV-B002', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7), 'total' => 2000000, 'remaining_amount' => 2000000, 'status' => 'unpaid',
        ]);

        $response = $this->actingAs($userA)->get('/portal/invoices');

        $response->assertOk();
        $response->assertSee('INV-A002');
        $response->assertDontSee('INV-B002');
    }

    public function test_tenant_cannot_verify_own_payment(): void
    {
        $this->seedRoles();
        $room = $this->makeRoom();
        [$userA, $tenantA] = $this->makeTenantWithUser('Tenant A');

        $response = $this->actingAs($userA)->get('/payments');

        $response->assertForbidden();
    }

    public function test_user_without_linked_tenant_record_is_forbidden_from_portal(): void
    {
        $this->seedRoles();
        $user = $this->makeUser('Tenant');

        $response = $this->actingAs($user)->get('/portal/dashboard');

        $response->assertForbidden();
    }
}
