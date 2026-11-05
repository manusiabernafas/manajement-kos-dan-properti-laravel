<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function verify(Payment $payment, int $verifiedBy): Payment
    {
        return DB::transaction(function () use ($payment, $verifiedBy) {
            if ($payment->status !== 'pending') {
                throw new RuntimeException('Pembayaran ini sudah diproses sebelumnya.');
            }

            $invoice = $payment->invoice()->lockForUpdate()->first();

            $payment->update([
                'status' => 'verified',
                'verified_by' => $verifiedBy,
                'verified_at' => now(),
            ]);

            $paidAmount = $invoice->paid_amount + $payment->amount;
            $remaining = max($invoice->total - $paidAmount, 0);

            $status = 'unpaid';
            if ($remaining <= 0) {
                $status = 'paid';
            } elseif ($paidAmount > 0) {
                $status = 'partial';
            }
            if ($status !== 'paid' && $invoice->due_date->isPast()) {
                $status = 'overdue';
            }

            $invoice->update([
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remaining,
                'status' => $status,
            ]);

            AuditLogService::log('verify_payment', 'payments', $payment->id, null, $payment->toArray());

            app(NotificationDispatchService::class)->paymentVerified($payment);

            return $payment->fresh();
        });
    }

    public function reject(Payment $payment, int $verifiedBy, ?string $notes = null): Payment
    {
        if ($payment->status !== 'pending') {
            throw new RuntimeException('Pembayaran ini sudah diproses sebelumnya.');
        }

        $payment->update([
            'status' => 'rejected',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'notes' => $notes ?? $payment->notes,
        ]);

        AuditLogService::log('reject_payment', 'payments', $payment->id, null, $payment->toArray());

        app(NotificationDispatchService::class)->paymentRejected($payment);

        return $payment->fresh();
    }
}
