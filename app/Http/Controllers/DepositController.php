<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Deposit::class);

        $deposits = Deposit::query()
            ->with(['tenant', 'room', 'contract'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('received_date')
            ->paginate(15)
            ->withQueryString();

        return view('deposits.index', ['title' => 'Deposit', 'deposits' => $deposits]);
    }

    public function show(Deposit $deposit): View
    {
        $this->authorize('view', $deposit);

        $deposit->load(['tenant', 'room', 'contract']);

        return view('deposits.show', ['title' => 'Detail Deposit', 'deposit' => $deposit]);
    }

    public function update(Request $request, Deposit $deposit): RedirectResponse
    {
        $this->authorize('update', $deposit);

        $validated = $request->validate([
            'refund_amount' => ['required', 'numeric', 'min:0'],
            'deduction' => ['required', 'numeric', 'min:0'],
            'refund_date' => ['required', 'date'],
            'status' => ['required', 'in:held,partially_refunded,refunded,forfeited'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $deposit->toArray();
        $deposit->update($validated);

        AuditLogService::log('update', 'deposits', $deposit->id, $old, $deposit->toArray());

        return back()->with('success', 'Data deposit berhasil diperbarui.');
    }
}
