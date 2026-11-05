<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Penalty;
use App\Models\PenaltyConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenaltyService
{
    public function markOverdueInvoices(): int
    {
        $invoices = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->whereDate('due_date', '<', Carbon::today())
            ->with('tenant.user')
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->update(['status' => 'overdue']);
            app(NotificationDispatchService::class)->invoiceOverdue($invoice);
        }

        return $invoices->count();
    }

    public function notifyDueSoonInvoices(): int
    {
        $invoices = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->whereDate('due_date', Carbon::today()->addDays(3))
            ->with('tenant.user')
            ->get();

        foreach ($invoices as $invoice) {
            app(NotificationDispatchService::class)->invoiceDueSoon($invoice);
        }

        return $invoices->count();
    }

    public function applyPenalties(): int
    {
        $count = 0;

        Invoice::where('status', 'overdue')
            ->whereDoesntHave('penaltyRecord')
            ->with('property')
            ->chunkById(100, function ($invoices) use (&$count) {
                foreach ($invoices as $invoice) {
                    if ($this->applyPenaltyToInvoice($invoice)) {
                        $count++;
                    }
                }
            });

        return $count;
    }

    protected function applyPenaltyToInvoice(Invoice $invoice): bool
    {
        $config = PenaltyConfig::where('status', 'active')
            ->where(fn ($q) => $q->where('property_id', $invoice->property_id)->orWhereNull('property_id'))
            ->orderByRaw('property_id IS NULL')
            ->first();

        if (! $config) {
            return false;
        }

        $daysLate = Carbon::today()->diffInDays($invoice->due_date, false) * -1;

        if ($daysLate <= $config->grace_period_days) {
            return false;
        }

        $amount = match ($config->calculation_type) {
            'fixed' => (float) $config->amount,
            'per_day' => (float) $config->amount * ($daysLate - $config->grace_period_days),
            'percentage' => round((float) $invoice->total * ((float) $config->amount / 100), 2),
            default => 0,
        };

        if ($amount <= 0) {
            return false;
        }

        return DB::transaction(function () use ($invoice, $config, $amount) {
            $created = Penalty::firstOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'penalty_config_id' => $config->id,
                    'amount' => $amount,
                    'reason' => 'Keterlambatan pembayaran',
                    'applied_date' => Carbon::today(),
                ]
            );

            if ($created->wasRecentlyCreated) {
                $invoice->increment('penalty', $amount);
                $invoice->increment('total', $amount);
                $invoice->increment('remaining_amount', $amount);

                return true;
            }

            return false;
        });
    }
}
