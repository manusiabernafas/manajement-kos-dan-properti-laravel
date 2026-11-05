<?php

namespace App\Http\Controllers\Tenant;

use App\Models\MaintenanceTicket;
use App\Services\AuditLogService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantMaintenanceController extends PortalController
{
    public function index(Request $request): View
    {
        $tenant = $this->tenant($request);

        $tickets = MaintenanceTicket::where('tenant_id', $tenant->id)->orderByDesc('created_at')->paginate(10);

        return view('tenant.maintenance.index', ['title' => 'Maintenance Saya', 'tickets' => $tickets]);
    }

    public function create(Request $request): View
    {
        $tenant = $this->tenant($request);
        $tenant->load('activeContract.room.property');

        return view('tenant.maintenance.create', ['title' => 'Ajukan Maintenance', 'tenant' => $tenant]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenant($request);
        abort_unless($tenant->activeContract, 422, 'Anda belum memiliki kamar aktif.');

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
        ]);

        $room = $tenant->activeContract->room;

        $ticket = MaintenanceTicket::create($validated + [
            'ticket_number' => NumberGeneratorService::generate('maintenance_tickets', 'ticket_number', 'MNT-'),
            'property_id' => $room->property_id,
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'status' => 'open',
        ]);

        AuditLogService::log('create', 'maintenance', $ticket->id, null, $ticket->toArray());

        return redirect()->route('tenant.maintenance.show', $ticket)->with('success', 'Permintaan maintenance berhasil dikirim.');
    }

    public function show(Request $request, MaintenanceTicket $maintenance): View
    {
        $tenant = $this->tenant($request);
        abort_unless($maintenance->tenant_id === $tenant->id, 403);

        $maintenance->load('comments.user');

        return view('tenant.maintenance.show', ['title' => $maintenance->ticket_number, 'ticket' => $maintenance]);
    }
}
