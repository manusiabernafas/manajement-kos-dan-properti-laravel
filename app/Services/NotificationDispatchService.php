<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Complaint;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\MaintenanceTicket;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\AnnouncementPublishedNotification;
use App\Notifications\ComplaintUpdatedNotification;
use App\Notifications\ContractExpiringNotification;
use App\Notifications\InvoiceCreatedNotification;
use App\Notifications\InvoiceDueSoonNotification;
use App\Notifications\InvoiceOverdueNotification;
use App\Notifications\MaintenanceUpdatedNotification;
use App\Notifications\PaymentRejectedNotification;
use App\Notifications\PaymentSubmittedNotification;
use App\Notifications\PaymentVerifiedNotification;

class NotificationDispatchService
{
    protected function financeUsers()
    {
        return User::role(['Super Admin', 'Owner', 'Finance'])->where('status', 'active')->get();
    }

    public function invoiceCreated(Invoice $invoice): void
    {
        $invoice->tenant->user?->notify(new InvoiceCreatedNotification($invoice));
    }

    public function invoiceDueSoon(Invoice $invoice): void
    {
        $invoice->tenant->user?->notify(new InvoiceDueSoonNotification($invoice));
    }

    public function invoiceOverdue(Invoice $invoice): void
    {
        $invoice->tenant->user?->notify(new InvoiceOverdueNotification($invoice));
    }

    public function paymentSubmitted(Payment $payment): void
    {
        foreach ($this->financeUsers() as $user) {
            $user->notify(new PaymentSubmittedNotification($payment));
        }
    }

    public function paymentVerified(Payment $payment): void
    {
        $payment->tenant->user?->notify(new PaymentVerifiedNotification($payment));
    }

    public function paymentRejected(Payment $payment): void
    {
        $payment->tenant->user?->notify(new PaymentRejectedNotification($payment));
    }

    public function maintenanceUpdated(MaintenanceTicket $ticket): void
    {
        $ticket->tenant?->user?->notify(new MaintenanceUpdatedNotification($ticket));
    }

    public function complaintUpdated(Complaint $complaint): void
    {
        $complaint->tenant->user?->notify(new ComplaintUpdatedNotification($complaint));
    }

    public function contractExpiring(Contract $contract): void
    {
        $contract->tenant->user?->notify(new ContractExpiringNotification($contract));
    }

    public function announcementPublished(Announcement $announcement): void
    {
        $tenantUsers = User::role('Tenant')->where('status', 'active')->get();

        foreach ($tenantUsers as $user) {
            $user->notify(new AnnouncementPublishedNotification($announcement));
        }
    }
}
