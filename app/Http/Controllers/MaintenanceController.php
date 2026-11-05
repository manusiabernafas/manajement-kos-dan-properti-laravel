<?php

namespace App\Http\Controllers;

use App\Http\Requests\Maintenance\StoreMaintenanceRequest;
use App\Models\MaintenanceTicket;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationDispatchService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MaintenanceTicket::class);

        $tickets = MaintenanceTicket::query()
            ->with(['property', 'room', 'assignee'])
            ->when($request->q, fn ($q) => $q->where('ticket_number', 'like', "%{$request->q}%")->orWhere('title', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->priority, fn ($q) => $q->where('priority', $request->priority))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('maintenance.index', ['title' => 'Maintenance', 'tickets' => $tickets]);
    }

    public function create(): View
    {
        $this->authorize('create', MaintenanceTicket::class);

        return view('maintenance.create', [
            'title' => 'Tambah Tiket Maintenance',
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
            'rooms' => Room::orderBy('room_number')->get(),
            'tenants' => Tenant::orderBy('full_name')->get(['id', 'full_name']),
            'technicians' => User::role(['Technician', 'Staff', 'Manager'])->orderBy('name')->get(),
        ]);
    }

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $ticket = MaintenanceTicket::create($request->validated() + [
            'ticket_number' => NumberGeneratorService::generate('maintenance_tickets', 'ticket_number', 'MNT-'),
            'status' => 'open',
            'created_by' => $request->user()->id,
        ]);

        AuditLogService::log('create', 'maintenance', $ticket->id, null, $ticket->toArray());

        return redirect()->route('maintenance.show', $ticket)->with('success', 'Tiket maintenance berhasil dibuat.');
    }

    public function show(MaintenanceTicket $maintenance): View
    {
        $this->authorize('view', $maintenance);

        $maintenance->load(['property', 'room', 'tenant', 'assignee', 'comments.user']);

        return view('maintenance.show', [
            'title' => $maintenance->ticket_number,
            'ticket' => $maintenance,
            'technicians' => User::role(['Technician', 'Staff', 'Manager'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MaintenanceTicket $maintenance): RedirectResponse
    {
        $this->authorize('update', $maintenance);

        $validated = $request->validate([
            'status' => ['required', 'in:open,assigned,in_progress,waiting,resolved,closed,cancelled'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $old = $maintenance->toArray();

        if (in_array($validated['status'], ['resolved', 'closed']) && ! $maintenance->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $maintenance->update($validated);

        AuditLogService::log('update', 'maintenance', $maintenance->id, $old, $maintenance->toArray());

        app(NotificationDispatchService::class)->maintenanceUpdated($maintenance);

        return back()->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function comment(Request $request, MaintenanceTicket $maintenance): RedirectResponse
    {
        $this->authorize('view', $maintenance);

        $request->validate(['comment' => ['required', 'string']]);

        $maintenance->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
