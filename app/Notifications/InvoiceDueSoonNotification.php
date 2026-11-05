<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceDueSoonNotification extends Notification
{
    use Queueable;

    public function __construct(protected Invoice $invoice)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'fa-clock',
            'title' => 'Tagihan Segera Jatuh Tempo',
            'message' => "Tagihan {$this->invoice->invoice_number} jatuh tempo pada ".$this->invoice->due_date->format('d M Y').'.',
            'url' => route('invoices.show', $this->invoice),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Pengingat: tagihan {$this->invoice->invoice_number} jatuh tempo ".$this->invoice->due_date->format('d M Y').'. Segera lakukan pembayaran.';
    }
}
