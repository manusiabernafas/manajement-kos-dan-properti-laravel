<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceOverdueNotification extends Notification
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
            'icon' => 'fa-triangle-exclamation',
            'title' => 'Tagihan Terlambat',
            'message' => "Tagihan {$this->invoice->invoice_number} sudah melewati tanggal jatuh tempo.",
            'url' => route('invoices.show', $this->invoice),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Tagihan {$this->invoice->invoice_number} sudah terlambat. Mohon segera diselesaikan.";
    }
}
