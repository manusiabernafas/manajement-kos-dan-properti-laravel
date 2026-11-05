<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Meter;
use App\Models\MeterReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MeterReadingService
{
    public function recordReading(Meter $meter, array $data, int $createdBy): MeterReading
    {
        $previousValue = (float) ($meter->latestReading()?->current_value ?? 0);
        $currentValue = (float) $data['current_value'];

        if ($currentValue < $previousValue) {
            throw new RuntimeException('Meter akhir tidak boleh lebih kecil dari meter awal ('.$previousValue.').');
        }

        $usage = $currentValue - $previousValue;
        $tariff = (float) ($data['tariff'] ?? $meter->tariff);
        $total = $usage * $tariff;

        return DB::transaction(function () use ($meter, $data, $previousValue, $currentValue, $usage, $tariff, $total, $createdBy) {
            $reading = MeterReading::create([
                'meter_id' => $meter->id,
                'reading_date' => $data['reading_date'],
                'previous_value' => $previousValue,
                'current_value' => $currentValue,
                'usage' => $usage,
                'tariff' => $tariff,
                'total' => $total,
                'photo_path' => $data['photo_path'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy,
            ]);

            if (! empty($data['generate_invoice']) && $total > 0) {
                $room = $meter->room;
                $contract = $room->activeContract;

                if ($contract) {
                    $invoice = Invoice::create([
                        'invoice_number' => NumberGeneratorService::generate('invoices', 'invoice_number', NumberGeneratorService::invoicePrefix()),
                        'tenant_id' => $contract->tenant_id,
                        'room_id' => $room->id,
                        'property_id' => $room->property_id,
                        'contract_id' => $contract->id,
                        'issue_date' => Carbon::parse($data['reading_date']),
                        'due_date' => Carbon::parse($data['reading_date'])->addDays(7),
                        'subtotal' => $total,
                        'total' => $total,
                        'remaining_amount' => $total,
                        'status' => 'unpaid',
                        'notes' => 'Tagihan '.$meter->meter_type.' otomatis dari pembacaan meter',
                        'created_by' => $createdBy,
                    ]);

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => $meter->meter_type,
                        'description' => ucfirst($meter->meter_type).' — '.$usage.' '.$meter->unit.' x Rp '.number_format($tariff, 0, ',', '.'),
                        'quantity' => $usage,
                        'price' => $tariff,
                        'subtotal' => $total,
                    ]);

                    $reading->update(['invoice_id' => $invoice->id]);
                }
            }

            AuditLogService::log('create', 'meters', $reading->id, null, $reading->toArray());

            return $reading->fresh();
        });
    }
}
