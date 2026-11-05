<?php

namespace App\Services;

use App\Models\CheckOut;
use App\Models\Contract;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckOutService
{
    public function process(array $data, int $createdBy): CheckOut
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $contract = Contract::lockForUpdate()->findOrFail($data['contract_id']);

            if ($contract->status !== 'active') {
                throw new RuntimeException('Kontrak ini tidak berstatus aktif.');
            }

            $room = $contract->room()->lockForUpdate()->first();
            $checkoutDate = Carbon::parse($data['checkout_date']);

            $outstandingAmount = (float) $contract->invoices()
                ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                ->sum('remaining_amount');

            $damageCost = (float) ($data['damage_cost'] ?? 0);
            $penaltyAmount = (float) ($data['penalty_amount'] ?? 0);

            $deposit = Deposit::where('contract_id', $contract->id)->where('status', 'held')->first();
            $depositAmount = $deposit ? (float) $deposit->amount : 0;

            $totalDeduction = $outstandingAmount + $damageCost + $penaltyAmount;
            $refundAmount = max($depositAmount - $totalDeduction, 0);
            $finalAmount = $depositAmount - $totalDeduction;

            if ($deposit) {
                $deposit->update([
                    'refund_amount' => $refundAmount,
                    'deduction' => min($totalDeduction, $depositAmount),
                    'refund_date' => $checkoutDate,
                    'status' => $refundAmount <= 0 ? 'forfeited' : ($refundAmount < $depositAmount ? 'partially_refunded' : 'refunded'),
                ]);
            }

            $contract->update(['status' => 'terminated']);

            $room->update(['status' => 'available']);

            $checkOut = CheckOut::create([
                'contract_id' => $contract->id,
                'tenant_id' => $contract->tenant_id,
                'room_id' => $room->id,
                'checkout_date' => $checkoutDate,
                'outstanding_amount' => $outstandingAmount,
                'penalty_amount' => $penaltyAmount,
                'damage_cost' => $damageCost,
                'deposit_amount' => $depositAmount,
                'refund_amount' => $refundAmount,
                'final_amount' => $finalAmount,
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy,
            ]);

            $tenant = $contract->tenant;
            if (! $tenant->contracts()->where('status', 'active')->exists()) {
                $tenant->update(['status' => 'inactive']);
            }

            AuditLogService::log('check_out', 'check-outs', $checkOut->id, null, $checkOut->toArray());

            return $checkOut->fresh();
        });
    }
}
