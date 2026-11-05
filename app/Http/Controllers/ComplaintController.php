<?php

namespace App\Http\Controllers;

use App\Http\Requests\Complaint\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationDispatchService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Complaint::class);

        $complaints = Complaint::query()
            ->with(['tenant', 'property', 'room', 'assignee'])
            ->when($request->q, fn ($q) => $q->where('ticket_number', 'like', "%{$request->q}%")->orWhere('title', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('complaints.index', ['title' => 'Komplain', 'complaints' => $complaints]);
    }

    public function create(): View
    {
        $this->authorize('create', Complaint::class);

        return view('complaints.create', [
            'title' => 'Tambah Komplain',
            'tenants' => Tenant::orderBy('full_name')->get(),
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $this->authorize('create', Complaint::class);

        $complaint = Complaint::create($request->validated() + [
            'ticket_number' => NumberGeneratorService::generate('complaints', 'ticket_number', 'CPL-'),
            'status' => 'open',
        ]);

        AuditLogService::log('create', 'complaints', $complaint->id, null, $complaint->toArray());

        return redirect()->route('complaints.show', $complaint)->with('success', 'Komplain berhasil dibuat.');
    }

    public function show(Complaint $complaint): View
    {
        $this->authorize('view', $complaint);

        $complaint->load(['tenant', 'property', 'room', 'assignee', 'comments.user']);

        return view('complaints.show', [
            'title' => $complaint->ticket_number,
            'complaint' => $complaint,
            'technicians' => User::role(['Technician', 'Staff', 'Manager'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('update', $complaint);

        $validated = $request->validate([
            'status' => ['required', 'in:open,assigned,in_progress,waiting,resolved,closed,cancelled'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $old = $complaint->toArray();

        if (in_array($validated['status'], ['resolved', 'closed']) && ! $complaint->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $complaint->update($validated);

        AuditLogService::log('update', 'complaints', $complaint->id, $old, $complaint->toArray());

        app(NotificationDispatchService::class)->complaintUpdated($complaint);

        return back()->with('success', 'Status komplain berhasil diperbarui.');
    }

    public function comment(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('view', $complaint);

        $request->validate(['comment' => ['required', 'string']]);

        $complaint->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
