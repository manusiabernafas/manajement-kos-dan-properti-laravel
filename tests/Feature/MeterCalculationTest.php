<?php

namespace Tests\Feature;

use App\Models\Meter;
use App\Services\MeterReadingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Support\CreatesPropertyStructure;
use Tests\TestCase;

class MeterCalculationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesPropertyStructure;

    public function test_meter_reading_calculates_usage_and_total_correctly(): void
    {
        $room = $this->makeRoom();
        $admin = \App\Models\User::factory()->create();
        $meter = Meter::create([
            'room_id' => $room->id, 'meter_type' => 'electricity', 'unit' => 'kWh', 'tariff' => 1500, 'status' => 'active',
        ]);

        $reading = app(MeterReadingService::class)->recordReading($meter, [
            'reading_date' => now()->format('Y-m-d'), 'current_value' => 120,
        ], $admin->id);

        $this->assertEquals(0, $reading->previous_value);
        $this->assertEquals(120, $reading->current_value);
        $this->assertEquals(120, $reading->usage);
        $this->assertEquals(120 * 1500, $reading->total);
    }

    public function test_second_reading_uses_previous_reading_as_baseline(): void
    {
        $room = $this->makeRoom();
        $admin = \App\Models\User::factory()->create();
        $meter = Meter::create([
            'room_id' => $room->id, 'meter_type' => 'electricity', 'unit' => 'kWh', 'tariff' => 1500, 'status' => 'active',
        ]);

        app(MeterReadingService::class)->recordReading($meter, [
            'reading_date' => now()->subMonth()->format('Y-m-d'), 'current_value' => 100,
        ], $admin->id);

        $second = app(MeterReadingService::class)->recordReading($meter, [
            'reading_date' => now()->format('Y-m-d'), 'current_value' => 150,
        ], $admin->id);

        $this->assertEquals(100, $second->previous_value);
        $this->assertEquals(50, $second->usage);
        $this->assertEquals(50 * 1500, $second->total);
    }

    public function test_meter_reading_rejects_value_lower_than_previous(): void
    {
        $room = $this->makeRoom();
        $admin = \App\Models\User::factory()->create();
        $meter = Meter::create([
            'room_id' => $room->id, 'meter_type' => 'water', 'unit' => 'm3', 'tariff' => 8000, 'status' => 'active',
        ]);

        app(MeterReadingService::class)->recordReading($meter, [
            'reading_date' => now()->subMonth()->format('Y-m-d'), 'current_value' => 50,
        ], $admin->id);

        $this->expectException(RuntimeException::class);

        app(MeterReadingService::class)->recordReading($meter, [
            'reading_date' => now()->format('Y-m-d'), 'current_value' => 30,
        ], $admin->id);
    }

    public function test_meter_reading_can_generate_invoice_when_requested(): void
    {
        $room = $this->makeRoom();
        $tenant = $this->makeTenant();
        $admin = \App\Models\User::factory()->create();

        app(\App\Services\CheckInService::class)->process([
            'tenant_id' => $tenant->id, 'room_id' => $room->id,
            'check_in_date' => now()->format('Y-m-d'), 'billing_date' => now()->addMonth()->format('Y-m-d'),
            'monthly_price' => $room->price, 'deposit' => $room->deposit, 'duration_months' => 12,
        ], $admin->id);

        $meter = Meter::create([
            'room_id' => $room->id, 'meter_type' => 'electricity', 'unit' => 'kWh', 'tariff' => 1500, 'status' => 'active',
        ]);

        $invoiceCountBefore = \App\Models\Invoice::count();

        $reading = app(MeterReadingService::class)->recordReading($meter, [
            'reading_date' => now()->format('Y-m-d'), 'current_value' => 100, 'generate_invoice' => true,
        ], $admin->id);

        $this->assertNotNull($reading->invoice_id);
        $this->assertEquals($invoiceCountBefore + 1, \App\Models\Invoice::count());
    }
}
