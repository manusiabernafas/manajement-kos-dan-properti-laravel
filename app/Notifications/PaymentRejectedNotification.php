<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'fa-circle-xmark',
            'title' => 'Pembayaran Ditolak',
            'message' => 'Pembayaran '.$this->payment->payment_number.' ditolak. '.($this->payment->notes ?? ''),
            'url' => route('tenant.payments.show', $this->payment),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        return 'Pembayaran Anda '.$this->payment->payment_number.' ditolak. Silakan hubungi pengelola untuk informasi lebih lanjut.';
    }
}
