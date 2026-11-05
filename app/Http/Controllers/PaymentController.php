<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AuditLogService;
use App\Services\NotificationDispatchService;
use App\Services\NumberGeneratorService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $payments = Payment::query()
            ->with(['invoice', 'tenant'])
            ->when($request->q, fn ($q) => $q->where('payment_number', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('payment_date')
            ->paginate(15)
            ->withQueryString();

        return view('payments.index', ['title' => 'Pembayaran', 'payments' => $payments]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Payment::class);

        $invoice = $request->invoice_id ? Invoice::with('tenant')->find($request->invoice_id) : null;

        return view('payments.create', [
            'title' => 'Catat Pembayaran',
            'invoices' => Invoice::whereIn('status', ['unpaid', 'partial', 'overdue'])->with('tenant')->get(),
            'selectedInvoice' => $invoice,
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($request->amount > $invoice->remaining_amount) {
            return back()->withInput()->with('error', 'Jumlah pembayaran tidak boleh melebihi sisa tagihan (Rp '.number_format($invoice->remaining_amount, 0, ',', '.').').');
        }

        $data = $request->validated();

        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('payment-proofs', 'public');
        }

        $payment = Payment::create([
            'payment_number' => NumberGeneratorService::generate('payments', 'payment_number', NumberGeneratorService::paymentPrefix()),
            'invoice_id' => $invoice->id,
            'tenant_id' => $invoice->tenant_id,
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'payment_method' => $data['payment_method'],
            'reference_number' => $data['reference_number'] ?? null,
            'proof_path' => $data['proof_path'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
            'created_by' => $request->user()->id,
        ]);

        AuditLogService::log('create', 'payments', $payment->id, null, $payment->toArray());

        app(NotificationDispatchService::class)->paymentSubmitted($payment);

        return redirect()->route('payments.show', $payment)->with('success', 'Pembayaran berhasil dicatat dan menunggu verifikasi.');
    }

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['invoice.tenant', 'tenant', 'verifier', 'creator']);

        return view('payments.show', ['title' => $payment->payment_number, 'payment' => $payment]);
    }

    public function verify(Request $request, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('verify', Payment::class);

        try {
            $service->verify($payment, $request->user()->id);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('verify', Payment::class);

        $request->validate(['notes' => ['nullable', 'string', 'max:500']]);

        try {
            $service->reject($payment, $request->user()->id, $request->notes);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('warning', 'Pembayaran ditolak.');
    }
}
