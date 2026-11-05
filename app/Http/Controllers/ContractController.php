<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Contract::class);

        $contracts = Contract::query()
            ->with(['tenant', 'room.property'])
            ->when($request->q, fn ($q) => $q->where('contract_number', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('start_date')
            ->paginate(15)
            ->withQueryString();

        return view('contracts.index', ['title' => 'Kontrak', 'contracts' => $contracts]);
    }

    public function show(Contract $contract): View
    {
        $this->authorize('view', $contract);

        $contract->load(['tenant', 'room.property', 'room.building', 'room.floor', 'invoices', 'deposits']);

        return view('contracts.show', ['title' => $contract->contract_number, 'contract' => $contract]);
    }

    public function edit(Contract $contract): View
    {
        $this->authorize('update', $contract);

        return view('contracts.edit', ['title' => 'Edit Kontrak', 'contract' => $contract]);
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorize('update', $contract);

        $validated = $request->validate([
            'end_date' => ['required', 'date', 'after:'.$contract->start_date->format('Y-m-d')],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'billing_day' => ['required', 'integer', 'min:1', 'max:31'],
            'terms' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active,expired,terminated'],
        ]);

        $old = $contract->toArray();
        $contract->update($validated);

        AuditLogService::log('update', 'contracts', $contract->id, $old, $contract->toArray());

        return redirect()->route('contracts.show', $contract)->with('success', 'Kontrak berhasil diperbarui.');
    }

    public function print(Contract $contract)
    {
        $this->authorize('view', $contract);

        $contract->load(['tenant', 'room.property']);

        $pdf = Pdf::loadView('contracts.pdf', ['contract' => $contract]);

        return $pdf->stream("Kontrak-{$contract->contract_number}.pdf");
    }
}
