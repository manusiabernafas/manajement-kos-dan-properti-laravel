<?php

namespace App\Notifications;

use App\Models\Contract;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContractExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(protected Contract $contract)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $url = $notifiable->hasRole('Tenant')
            ? route('tenant.contract')
            : route('contracts.show', $this->contract);

        return [
            'icon' => 'fa-file-signature',
            'title' => 'Kontrak Akan Berakhir',
            'message' => "Kontrak {$this->contract->contract_number} akan berakhir pada ".$this->contract->end_date->format('d M Y').'.',
            'url' => $url,
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Kontrak Anda {$this->contract->contract_number} akan berakhir pada ".$this->contract->end_date->format('d M Y').'. Silakan hubungi pengelola untuk perpanjangan.';
    }
}
