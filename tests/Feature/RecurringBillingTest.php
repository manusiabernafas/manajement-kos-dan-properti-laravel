<?php

namespace Tests\Feature;

use App\Services\CheckInService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesPropertyStructure;
use Tests\TestCase;

class RecurringBillingTest extends TestCase
{
    use RefreshDatabase;
    use CreatesPropertyStructure;

    public function test_billing_generate_creates_invoice_for_active_contract_past_billing_day(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();

        // Check in a year ago so the contract has been active across many billing cycles already.
        $checkInDate = Carbon::now()->subYear();
        app(CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'billing_date' => $checkInDate->copy()->addMonth()->day(5)->format('Y-m-d'),
            'monthly_price' => $room->price, 'deposit' => $room->deposit, 'duration_months' => 24,
        ], $admin->id);

        $invoiceCountBefore = \App\Models\Invoice::count();

        $this->artisan('billing:generate')->assertSuccessful();

        $this->assertGreaterThan($invoiceCountBefore, \App\Models\Invoice::count());
    }

    public function test_billing_generate_does_not_create_duplicate_invoice_for_same_period(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();

        $checkInDate = Carbon::now()->subYear();
        app(CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'billing_date' => $checkInDate->copy()->addMonth()->day(5)->format('Y-m-d'),
            'monthly_price' => $room->price, 'deposit' => $room->deposit, 'duration_months' => 24,
        ], $admin->id);

        $this->artisan('billing:generate');
        $countAfterFirstRun = \App\Models\Invoice::count();

        $this->artisan('billing:generate');
        $countAfterSecondRun = \App\Models\Invoice::count();

        $this->assertEquals($countAfterFirstRun, $countAfterSecondRun);
    }

    public function test_billing_generate_marks_past_due_invoices_as_overdue(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();

        $invoice = $tenant->invoices()->create([
            'invoice_number' => 'INV-OVERDUE-1', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now()->subDays(20), 'due_date' => now()->subDays(10),
            'total' => 1000000, 'remaining_amount' => 1000000, 'status' => 'unpaid',
        ]);

        $this->artisan('billing:generate');

        $this->assertEquals('overdue', $invoice->fresh()->status);
    }
}
