<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'fa-money-check-dollar',
            'title' => 'Pembayaran Baru Diajukan',
            'message' => "{$this->payment->tenant->full_name} mengajukan pembayaran sebesar Rp ".number_format($this->payment->amount, 0, ',', '.').'.',
            'url' => route('payments.show', $this->payment),
        ];
    }
}
