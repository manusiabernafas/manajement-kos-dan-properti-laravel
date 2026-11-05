<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function generateRecurringInvoices(?Carbon $referenceDate = null): int
    {
        $today = $referenceDate ?? Carbon::today();
        $generated = 0;

        Contract::where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->with('room')
            ->chunkById(100, function ($contracts) use ($today, &$generated) {
                foreach ($contracts as $contract) {
                    if ($this->generateForContract($contract, $today)) {
                        $generated++;
                    }
                }
            });

        return $generated;
    }

    protected function generateForContract(Contract $contract, Carbon $today): bool
    {
        $billingDay = min($contract->billing_day, $today->daysInMonth);

        if ($today->day < $billingDay) {
            return false;
        }

        $exists = Invoice::where('contract_id', $contract->id)
            ->where('period_month', $today->month)
            ->where('period_year', $today->year)
            ->exists();

        if ($exists) {
            return false;
        }

        $dueDate = $today->copy()->day($billingDay);

        return DB::transaction(function () use ($contract, $today, $dueDate) {
            $invoice = Invoice::create([
                'invoice_number' => NumberGeneratorService::generate('invoices', 'invoice_number', NumberGeneratorService::invoicePrefix()),
                'tenant_id' => $contract->tenant_id,
                'room_id' => $contract->room_id,
                'property_id' => $contract->property_id,
                'contract_id' => $contract->id,
                'issue_date' => $today,
                'due_date' => $dueDate,
                'period_month' => $today->month,
                'period_year' => $today->year,
                'subtotal' => $contract->monthly_price,
                'total' => $contract->monthly_price,
                'remaining_amount' => $contract->monthly_price,
                'status' => 'unpaid',
                'notes' => 'Tagihan sewa bulanan otomatis',
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'rent',
                'description' => 'Sewa kamar '.$contract->room->room_number.' — '.$today->translatedFormat('F Y'),
                'quantity' => 1,
                'price' => $contract->monthly_price,
                'subtotal' => $contract->monthly_price,
            ]);

            AuditLogService::log('create', 'invoices', $invoice->id, null, $invoice->toArray());

            app(NotificationDispatchService::class)->invoiceCreated($invoice);

            return true;
        });
    }
}
