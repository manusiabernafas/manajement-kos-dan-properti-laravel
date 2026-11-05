<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CheckIn;
use App\Models\Contract;
use App\Models\Deposit;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckInService
{
    public function process(array $data, int $createdBy): Contract
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $room = Room::lockForUpdate()->findOrFail($data['room_id']);

            if (! in_array($room->status, ['available', 'reserved'], true)) {
                throw new RuntimeException('Kamar tidak tersedia untuk check-in.');
            }

            $tenant = Tenant::findOrFail($data['tenant_id']);

            if ($tenant->contracts()->where('status', 'active')->exists()) {
                throw new RuntimeException('Penghuni sudah memiliki kontrak aktif pada kamar lain.');
            }

            $checkInDate = Carbon::parse($data['check_in_date']);
            $billingDate = Carbon::parse($data['billing_date']);
            $endDate = $checkInDate->copy()->addMonths((int) ($data['duration_months'] ?? 12));

            $contract = Contract::create([
                'contract_number' => NumberGeneratorService::generate('contracts', 'contract_number', NumberGeneratorService::contractPrefix()),
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'property_id' => $room->property_id,
                'booking_id' => $data['booking_id'] ?? null,
                'start_date' => $checkInDate,
                'end_date' => $endDate,
                'monthly_price' => $data['monthly_price'],
                'deposit' => $data['deposit'],
                'billing_day' => $billingDate->day,
                'terms' => $data['terms'] ?? null,
                'status' => 'active',
                'created_by' => $createdBy,
            ]);

            $room->update(['status' => 'occupied']);

            Deposit::create([
                'tenant_id' => $tenant->id,
                'contract_id' => $contract->id,
                'room_id' => $room->id,
                'amount' => $data['deposit'],
                'received_date' => $checkInDate,
                'status' => 'held',
                'notes' => 'Deposit awal check-in',
            ]);

            $invoice = Invoice::create([
                'invoice_number' => NumberGeneratorService::generate('invoices', 'invoice_number', NumberGeneratorService::invoicePrefix()),
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'property_id' => $room->property_id,
                'contract_id' => $contract->id,
                'issue_date' => $checkInDate,
                'due_date' => $billingDate,
                'period_month' => $billingDate->month,
                'period_year' => $billingDate->year,
                'subtotal' => $data['monthly_price'],
                'total' => $data['monthly_price'],
                'remaining_amount' => $data['monthly_price'],
                'status' => 'unpaid',
                'notes' => 'Tagihan sewa pertama saat check-in',
                'created_by' => $createdBy,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'rent',
                'description' => 'Sewa kamar '.$room->room_number.' — '.$checkInDate->translatedFormat('F Y'),
                'quantity' => 1,
                'price' => $data['monthly_price'],
                'subtotal' => $data['monthly_price'],
            ]);

            CheckIn::create([
                'contract_id' => $contract->id,
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'booking_id' => $data['booking_id'] ?? null,
                'check_in_date' => $checkInDate,
                'monthly_price' => $data['monthly_price'],
                'deposit' => $data['deposit'],
                'billing_date' => $billingDate,
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy,
            ]);

            if (! empty($data['booking_id'])) {
                Booking::where('id', $data['booking_id'])->update(['status' => 'checked_in']);
            }

            AuditLogService::log('check_in', 'check-ins', $contract->id, null, $contract->toArray());

            app(NotificationDispatchService::class)->invoiceCreated($invoice);

            return $contract->fresh();
        });
    }
}
