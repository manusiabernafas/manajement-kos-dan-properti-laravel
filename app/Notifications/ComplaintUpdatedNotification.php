<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ComplaintUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Complaint $complaint)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $url = $notifiable->hasRole('Tenant')
            ? route('tenant.complaints.show', $this->complaint)
            : route('complaints.show', $this->complaint);

        return [
            'icon' => 'fa-comment-dots',
            'title' => 'Update Komplain',
            'message' => "Komplain {$this->complaint->ticket_number} sekarang berstatus ".ucfirst(str_replace('_', ' ', $this->complaint->status)).'.',
            'url' => $url,
        ];
    }
}
