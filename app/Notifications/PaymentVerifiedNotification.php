<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification
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
            'icon' => 'fa-circle-check',
            'title' => 'Pembayaran Diverifikasi',
            'message' => 'Pembayaran '.$this->payment->payment_number.' sebesar Rp '.number_format($this->payment->amount, 0, ',', '.').' telah diverifikasi.',
            'url' => route('tenant.payments.show', $this->payment),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        return 'Pembayaran Anda sebesar Rp '.number_format($this->payment->amount, 0, ',', '.').' telah berhasil diverifikasi. Terima kasih.';
    }
}
