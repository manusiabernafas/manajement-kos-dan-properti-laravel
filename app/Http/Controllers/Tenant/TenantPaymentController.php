<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AuditLogService;
use App\Services\NotificationDispatchService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantPaymentController extends PortalController
{
    public function index(Request $request): View
    {
        $tenant = $this->tenant($request);

        $payments = $tenant->payments()->with('invoice')->orderByDesc('payment_date')->paginate(10);

        return view('tenant.payments.index', ['title' => 'Pembayaran Saya', 'payments' => $payments]);
    }

    public function create(Request $request): View
    {
        $tenant = $this->tenant($request);

        $invoices = $tenant->invoices()->whereIn('status', ['unpaid', 'partial', 'overdue'])->get();

        return view('tenant.payments.create', [
            'title' => 'Ajukan Pembayaran',
            'invoices' => $invoices,
            'selectedInvoice' => $request->invoice_id ? $invoices->firstWhere('id', (int) $request->invoice_id) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenant($request);

        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer,qris,e_wallet,virtual_account,other'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        abort_unless($invoice->tenant_id === $tenant->id, 403);

        if ($validated['amount'] > $invoice->remaining_amount) {
            return back()->withInput()->with('error', 'Jumlah pembayaran melebihi sisa tagihan.');
        }

        $validated['proof_path'] = $request->file('proof')->store('payment-proofs', 'public');

        $payment = Payment::create([
            'payment_number' => NumberGeneratorService::generate('payments', 'payment_number', NumberGeneratorService::paymentPrefix()),
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'proof_path' => $validated['proof_path'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        AuditLogService::log('create', 'payments', $payment->id, null, $payment->toArray());

        app(NotificationDispatchService::class)->paymentSubmitted($payment);

        return redirect()->route('tenant.payments.show', $payment)->with('success', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi.');
    }

    public function show(Request $request, Payment $payment): View
    {
        $tenant = $this->tenant($request);
        abort_unless($payment->tenant_id === $tenant->id, 403);

        $payment->load('invoice');

        return view('tenant.payments.show', ['title' => $payment->payment_number, 'payment' => $payment]);
    }
}
