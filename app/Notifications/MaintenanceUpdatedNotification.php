<?php

namespace App\Notifications;

use App\Models\MaintenanceTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MaintenanceUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(protected MaintenanceTicket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $url = $notifiable->hasRole('Tenant')
            ? route('tenant.maintenance.show', $this->ticket)
            : route('maintenance.show', $this->ticket);

        return [
            'icon' => 'fa-screwdriver-wrench',
            'title' => 'Update Maintenance',
            'message' => "Tiket {$this->ticket->ticket_number} sekarang berstatus ".ucfirst(str_replace('_', ' ', $this->ticket->status)).'.',
            'url' => $url,
        ];
    }
}
