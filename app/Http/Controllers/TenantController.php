<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tenant\StoreTenantRequest;
use App\Http\Requests\Tenant\UpdateTenantRequest;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tenant::class);

        $tenants = Tenant::query()
            ->with('activeContract.room')
            ->when($request->q, fn ($q) => $q->where(fn ($sub) => $sub
                ->where('full_name', 'like', "%{$request->q}%")
                ->orWhere('tenant_code', 'like', "%{$request->q}%")
                ->orWhere('phone', 'like', "%{$request->q}%")))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('tenants.index', ['title' => 'Penghuni', 'tenants' => $tenants]);
    }

    public function create(): View
    {
        $this->authorize('create', Tenant::class);

        return view('tenants.create', ['title' => 'Tambah Penghuni']);
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_code'] = $this->generateTenantCode();
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant = Tenant::create($data);

        AuditLogService::log('create', 'tenants', $tenant->id, null, $tenant->toArray());

        return redirect()->route('tenants.show', $tenant)->with('success', 'Penghuni berhasil ditambahkan.');
    }

    public function show(Tenant $tenant): View
    {
        $this->authorize('view', $tenant);

        $tenant->load([
            'activeContract.room.property', 'contracts.room',
            'documents', 'invoices' => fn ($q) => $q->latest()->limit(15),
            'payments' => fn ($q) => $q->latest()->limit(15),
            'complaints' => fn ($q) => $q->latest()->limit(10),
            'maintenanceTickets' => fn ($q) => $q->latest()->limit(10),
        ]);

        $activities = AuditLog::where(fn ($q) => $q->where('module', 'tenants')->where('record_id', $tenant->id))
            ->orWhere(fn ($q) => $q->where('module', 'invoices')->whereIn('record_id', $tenant->invoices->pluck('id')))
            ->latest()->limit(20)->get();

        return view('tenants.show', ['title' => $tenant->full_name, 'tenant' => $tenant, 'activities' => $activities]);
    }

    public function edit(Tenant $tenant): View
    {
        $this->authorize('update', $tenant);

        return view('tenants.edit', ['title' => 'Edit Penghuni', 'tenant' => $tenant]);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $old = $tenant->toArray();
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($tenant->photo) {
                Storage::disk('public')->delete($tenant->photo);
            }
            $data['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant->update($data);

        AuditLogService::log('update', 'tenants', $tenant->id, $old, $tenant->toArray());

        return redirect()->route('tenants.show', $tenant)->with('success', 'Data penghuni berhasil diperbarui.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->authorize('delete', $tenant);

        if ($tenant->invoices()->exists() || $tenant->contracts()->exists()) {
            return back()->with('error', 'Penghuni tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        $tenant->delete();

        AuditLogService::log('delete', 'tenants', $tenant->id, $tenant->toArray(), null);

        return redirect()->route('tenants.index')->with('success', 'Penghuni berhasil dihapus.');
    }

    protected function generateTenantCode(): string
    {
        $prefix = \App\Services\NumberGeneratorService::tenantPrefix();
        $last = Tenant::withTrashed()->where('tenant_code', 'like', $prefix.'%')->orderByDesc('id')->first();
        $number = $last ? ((int) str_replace($prefix, '', $last->tenant_code)) + 1 : 1;

        return $prefix.str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
