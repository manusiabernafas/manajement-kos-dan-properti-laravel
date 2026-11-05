<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Services\BillingService;
use App\Services\NotificationDispatchService;
use App\Services\PenaltyService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateBilling extends Command
{
    protected $signature = 'billing:generate';

    protected $description = 'Generate tagihan sewa bulanan untuk seluruh kontrak aktif, tandai invoice overdue, terapkan denda keterlambatan, dan kirim notifikasi terkait';

    public function handle(BillingService $billingService, PenaltyService $penaltyService, NotificationDispatchService $notifier): int
    {
        $this->info('Menjalankan generate billing...');

        $generated = $billingService->generateRecurringInvoices();
        $this->info("Invoice baru dibuat: {$generated}");

        $dueSoon = $penaltyService->notifyDueSoonInvoices();
        $this->info("Notifikasi jatuh tempo dikirim: {$dueSoon}");

        $overdue = $penaltyService->markOverdueInvoices();
        $this->info("Invoice ditandai overdue: {$overdue}");

        $penalized = $penaltyService->applyPenalties();
        $this->info("Denda diterapkan: {$penalized}");

        $expiringContracts = Contract::where('status', 'active')
            ->whereDate('end_date', Carbon::today()->addDays(30))
            ->with('tenant.user')
            ->get();

        foreach ($expiringContracts as $contract) {
            $notifier->contractExpiring($contract);
        }
        $this->info('Notifikasi kontrak akan berakhir dikirim: '.$expiringContracts->count());

        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
