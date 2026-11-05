<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Abstraction layer for WhatsApp delivery. No real provider is wired up yet —
 * this logs the outgoing message so a provider (e.g. Fonnte, Twilio, WABA)
 * can be dropped in later by replacing the body of send().
 */
class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $phone = $notifiable->routeNotificationForWhatsApp() ?? null;

        if (! $phone) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        Log::channel('stack')->info('[WhatsApp] Pesan ke '.$phone.': '.$message);
    }
}
