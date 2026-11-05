<?php

namespace Tests\Feature;

use App\Services\CheckInService;
use App\Services\CheckOutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Support\CreatesPropertyStructure;
use Tests\Support\SeedsRolesAndPermissions;
use Tests\TestCase;

class BookingCheckInCheckOutTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;
    use CreatesPropertyStructure;

    public function test_staff_can_create_booking(): void
    {
        $this->seedRoles();
        $staff = $this->makeUser('Staff');
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();

        $response = $this->actingAs($staff)->post('/bookings', [
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'booking_date' => now()->format('Y-m-d'), 'check_in_date' => now()->addDay()->format('Y-m-d'),
            'monthly_price' => $room->price, 'deposit' => $room->deposit,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', ['tenant_id' => $tenant->id, 'room_id' => $room->id, 'status' => 'inquiry']);
    }

    public function test_confirming_booking_reserves_the_room(): void
    {
        $this->seedRoles();
        $staff = $this->makeUser('Staff');
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $booking = \App\Models\Booking::create([
            'booking_number' => 'BKG-00001', 'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'booking_date' => now(), 'check_in_date' => now()->addDay(),
            'monthly_price' => $room->price, 'deposit' => $room->deposit, 'status' => 'inquiry',
        ]);

        $this->actingAs($staff)->post("/bookings/{$booking->id}/confirm");

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'reserved']);
    }

    public function test_check_in_creates_contract_deposit_and_first_invoice(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();

        $contract = app(CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'check_in_date' => now()->format('Y-m-d'), 'billing_date' => now()->addMonth()->format('Y-m-d'),
            'monthly_price' => $room->price, 'deposit' => $room->deposit, 'duration_months' => 12,
        ], $admin->id);

        $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'status' => 'active']);
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'occupied']);
        $this->assertDatabaseHas('deposits', ['contract_id' => $contract->id, 'status' => 'held']);
        $this->assertDatabaseCount('invoices', 1);

        $invoice = $contract->invoices()->first();
        $this->assertEquals($room->price, $invoice->total);
        $this->assertEquals('unpaid', $invoice->status);
    }

    public function test_check_in_fails_when_room_is_not_available(): void
    {
        $room = $this->makeRoom(['status' => 'occupied']);
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();

        $this->expectException(RuntimeException::class);

        app(CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'check_in_date' => now()->format('Y-m-d'), 'billing_date' => now()->addMonth()->format('Y-m-d'),
            'monthly_price' => $room->price, 'deposit' => $room->deposit, 'duration_months' => 12,
        ], $admin->id);
    }

    public function test_tenant_cannot_have_two_active_contracts(): void
    {
        $roomOne = $this->makeRoom();
        $roomTwo = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();

        app(CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $roomOne->id,
            'check_in_date' => now()->format('Y-m-d'), 'billing_date' => now()->addMonth()->format('Y-m-d'),
            'monthly_price' => $roomOne->price, 'deposit' => $roomOne->deposit, 'duration_months' => 12,
        ], $admin->id);

        $this->expectException(RuntimeException::class);

        app(CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $roomTwo->id,
            'check_in_date' => now()->format('Y-m-d'), 'billing_date' => now()->addMonth()->format('Y-m-d'),
            'monthly_price' => $roomTwo->price, 'deposit' => $roomTwo->deposit, 'duration_months' => 12,
        ], $admin->id);
    }

    public function test_check_out_frees_room_and_refunds_deposit(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();

        $contract = app(CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'check_in_date' => now()->format('Y-m-d'), 'billing_date' => now()->addMonth()->format('Y-m-d'),
            'monthly_price' => $room->price, 'deposit' => $room->deposit, 'duration_months' => 12,
        ], $admin->id);

        // Pay off the first invoice so there is no outstanding balance.
        $invoice = $contract->invoices()->first();
        $invoice->update(['paid_amount' => $invoice->total, 'remaining_amount' => 0, 'status' => 'paid']);

        $checkOut = app(CheckOutService::class)->process([
            'contract_id' => $contract->id, 'checkout_date' => now()->addDays(5)->format('Y-m-d'),
            'damage_cost' => 0, 'penalty_amount' => 0, 'reason' => 'Pindah kerja',
        ], $admin->id);

        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'available']);
        $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'status' => 'terminated']);
        $this->assertEquals($room->deposit, $checkOut->refund_amount);
        $this->assertDatabaseHas('deposits', ['contract_id' => $contract->id, 'status' => 'refunded']);
    }

    public function test_check_out_deducts_outstanding_invoice_from_deposit(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();

        $contract = app(CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'check_in_date' => now()->format('Y-m-d'), 'billing_date' => now()->addMonth()->format('Y-m-d'),
            'monthly_price' => $room->price, 'deposit' => $room->deposit, 'duration_months' => 12,
        ], $admin->id);

        // First invoice ($room->price) stays unpaid, deposit equals $room->deposit (same amount as price here).
        $checkOut = app(CheckOutService::class)->process([
            'contract_id' => $contract->id, 'checkout_date' => now()->addDays(5)->format('Y-m-d'),
            'damage_cost' => 0, 'penalty_amount' => 0,
        ], $admin->id);

        $this->assertEquals(0, $checkOut->refund_amount);
        $this->assertDatabaseHas('deposits', ['contract_id' => $contract->id, 'status' => 'forfeited']);
    }
}
