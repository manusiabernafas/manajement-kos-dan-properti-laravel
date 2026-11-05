<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceCreatedNotification extends Notification
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
            'icon' => 'fa-file-invoice-dollar',
            'title' => 'Tagihan Baru',
            'message' => "Tagihan {$this->invoice->invoice_number} sebesar Rp ".number_format($this->invoice->total, 0, ',', '.').' telah dibuat.',
            'url' => route('invoices.show', $this->invoice),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Tagihan baru {$this->invoice->invoice_number} sebesar Rp ".number_format($this->invoice->total, 0, ',', '.').' jatuh tempo '.$this->invoice->due_date->format('d M Y').'.';
    }
}
