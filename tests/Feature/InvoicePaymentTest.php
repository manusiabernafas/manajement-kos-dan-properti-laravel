<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Services\CheckInService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesPropertyStructure;
use Tests\Support\SeedsRolesAndPermissions;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;
    use CreatesPropertyStructure;

    public function test_finance_can_create_manual_invoice(): void
    {
        $this->seedRoles();
        $finance = $this->makeUser('Finance');
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();

        $response = $this->actingAs($finance)->post('/invoices', [
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'issue_date' => now()->format('Y-m-d'), 'due_date' => now()->addDays(7)->format('Y-m-d'),
            'items' => [
                ['item_type' => 'rent', 'description' => 'Sewa bulan ini', 'quantity' => 1, 'price' => 1500000],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', ['tenant_id' => $tenant->id, 'total' => 1500000, 'status' => 'unpaid']);
    }

    public function test_payment_cannot_exceed_outstanding_invoice_amount(): void
    {
        $this->seedRoles();
        $finance = $this->makeUser('Finance');
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $invoice = $tenant->invoices()->create([
            'invoice_number' => 'INV-00001', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7),
            'total' => 500000, 'remaining_amount' => 500000, 'status' => 'unpaid',
        ]);

        $response = $this->actingAs($finance)->post('/payments', [
            'invoice_id' => $invoice->id, 'amount' => 600000, 'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_verifying_payment_updates_invoice_paid_and_remaining_amount(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();
        $invoice = $tenant->invoices()->create([
            'invoice_number' => 'INV-00002', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7),
            'total' => 1500000, 'remaining_amount' => 1500000, 'status' => 'unpaid',
        ]);
        $payment = Payment::create([
            'payment_number' => 'PAY-00001', 'invoice_id' => $invoice->id, 'tenant_id' => $tenant->id,
            'amount' => 1500000, 'payment_date' => now(), 'payment_method' => 'cash', 'status' => 'pending',
        ]);

        app(PaymentService::class)->verify($payment, $admin->id);

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(1500000, $invoice->paid_amount);
        $this->assertEquals(0, $invoice->remaining_amount);
        $this->assertEquals('verified', $payment->fresh()->status);
    }

    public function test_partial_payment_sets_invoice_status_to_partial(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();
        $invoice = $tenant->invoices()->create([
            'invoice_number' => 'INV-00003', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7),
            'total' => 1000000, 'remaining_amount' => 1000000, 'status' => 'unpaid',
        ]);
        $payment = Payment::create([
            'payment_number' => 'PAY-00002', 'invoice_id' => $invoice->id, 'tenant_id' => $tenant->id,
            'amount' => 400000, 'payment_date' => now(), 'payment_method' => 'cash', 'status' => 'pending',
        ]);

        app(PaymentService::class)->verify($payment, $admin->id);

        $invoice->refresh();
        $this->assertEquals('partial', $invoice->status);
        $this->assertEquals(600000, $invoice->remaining_amount);
    }

    public function test_invoice_with_payment_cannot_be_cancelled(): void
    {
        $this->seedRoles();
        $finance = $this->makeUser('Finance');
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $invoice = $tenant->invoices()->create([
            'invoice_number' => 'INV-00004', 'room_id' => $room->id, 'property_id' => $room->property_id,
            'issue_date' => now(), 'due_date' => now()->addDays(7),
            'total' => 1000000, 'paid_amount' => 200000, 'remaining_amount' => 800000, 'status' => 'partial',
        ]);

        $response = $this->actingAs($finance)->post("/invoices/{$invoice->id}/cancel");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'partial']);
    }
}
