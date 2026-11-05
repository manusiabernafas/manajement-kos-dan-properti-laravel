<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantDashboardController extends PortalController
{
    public function index(Request $request): View
    {
        $tenant = $this->tenant($request);
        $tenant->load(['activeContract.room.property']);

        $unpaidInvoices = $tenant->invoices()->whereIn('status', ['unpaid', 'partial', 'overdue'])->orderBy('due_date')->get();
        $totalUnpaid = $unpaidInvoices->sum('remaining_amount');
        $nextDue = $unpaidInvoices->first();

        $announcements = \App\Models\Announcement::active()
            ->where(function ($q) use ($tenant) {
                $q->where('target_type', 'all');
                if ($tenant->activeContract) {
                    $room = $tenant->activeContract->room;
                    $q->orWhere(fn ($sub) => $sub->where('target_type', 'property')->where('property_id', $room->property_id))
                        ->orWhere(fn ($sub) => $sub->where('target_type', 'building')->where('building_id', $room->building_id))
                        ->orWhere(fn ($sub) => $sub->where('target_type', 'floor')->where('floor_id', $room->floor_id))
                        ->orWhere(fn ($sub) => $sub->where('target_type', 'room')->where('room_id', $room->id));
                }
            })
            ->latest()->limit(5)->get();

        return view('tenant.dashboard.index', [
            'title' => 'Dashboard',
            'tenant' => $tenant,
            'unpaidInvoices' => $unpaidInvoices,
            'totalUnpaid' => $totalUnpaid,
            'nextDue' => $nextDue,
            'announcements' => $announcements,
        ]);
    }

    public function room(Request $request): View
    {
        $tenant = $this->tenant($request);
        $tenant->load('activeContract.room.property', 'activeContract.room.building', 'activeContract.room.floor', 'activeContract.room.facilities');

        return view('tenant.dashboard.room', ['title' => 'Kamar Saya', 'tenant' => $tenant]);
    }

    public function contract(Request $request): View
    {
        $tenant = $this->tenant($request);
        $tenant->load('activeContract.room.property');

        return view('tenant.dashboard.contract', ['title' => 'Kontrak Saya', 'tenant' => $tenant]);
    }
}
