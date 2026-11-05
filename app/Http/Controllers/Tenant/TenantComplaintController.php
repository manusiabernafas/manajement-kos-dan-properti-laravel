<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Complaint;
use App\Services\AuditLogService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantComplaintController extends PortalController
{
    public function index(Request $request): View
    {
        $tenant = $this->tenant($request);

        $complaints = Complaint::where('tenant_id', $tenant->id)->orderByDesc('created_at')->paginate(10);

        return view('tenant.complaints.index', ['title' => 'Komplain Saya', 'complaints' => $complaints]);
    }

    public function create(Request $request): View
    {
        $tenant = $this->tenant($request);

        return view('tenant.complaints.create', ['title' => 'Buat Komplain', 'tenant' => $tenant]);
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

        $complaint = Complaint::create($validated + [
            'ticket_number' => NumberGeneratorService::generate('complaints', 'ticket_number', 'CPL-'),
            'tenant_id' => $tenant->id,
            'property_id' => $room->property_id,
            'room_id' => $room->id,
            'status' => 'open',
        ]);

        AuditLogService::log('create', 'complaints', $complaint->id, null, $complaint->toArray());

        return redirect()->route('tenant.complaints.show', $complaint)->with('success', 'Komplain berhasil dikirim.');
    }

    public function show(Request $request, Complaint $complaint): View
    {
        $tenant = $this->tenant($request);
        abort_unless($complaint->tenant_id === $tenant->id, 403);

        $complaint->load('comments.user');

        return view('tenant.complaints.show', ['title' => $complaint->ticket_number, 'complaint' => $complaint]);
    }
}
