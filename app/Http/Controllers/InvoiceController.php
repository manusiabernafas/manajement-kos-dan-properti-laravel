<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Property;
use App\Models\Tenant;
use App\Services\AuditLogService;
use App\Services\NumberGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $invoices = Invoice::query()
            ->with(['tenant', 'room', 'property'])
            ->when($request->q, fn ($q) => $q->where('invoice_number', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->property_id, fn ($q) => $q->where('property_id', $request->property_id))
            ->when($request->tenant_id, fn ($q) => $q->where('tenant_id', $request->tenant_id))
            ->orderByDesc('issue_date')
            ->paginate(15)
            ->withQueryString();

        return view('invoices.index', [
            'title' => 'Tagihan',
            'invoices' => $invoices,
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Invoice::class);

        return view('invoices.create', [
            'title' => 'Buat Tagihan',
            'tenants' => Tenant::where('status', 'active')->orderBy('full_name')->get(),
            'contracts' => Contract::where('status', 'active')->with(['tenant', 'room'])->get(),
            'rooms' => \App\Models\Room::orderBy('room_number')->get(),
        ]);
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $room = \App\Models\Room::findOrFail($data['room_id']);

        $subtotal = collect($data['items'])->sum(fn ($item) => $item['quantity'] * $item['price']);
        $discount = $data['discount'] ?? 0;
        $tax = $data['tax'] ?? 0;
        $total = $subtotal - $discount + $tax;

        $invoice = DB::transaction(function () use ($data, $room, $subtotal, $discount, $tax, $total, $request) {
            $invoice = Invoice::create([
                'invoice_number' => NumberGeneratorService::generate('invoices', 'invoice_number', NumberGeneratorService::invoicePrefix()),
                'tenant_id' => $data['tenant_id'],
                'room_id' => $room->id,
                'property_id' => $room->property_id,
                'contract_id' => $data['contract_id'] ?? null,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'remaining_amount' => $total,
                'status' => 'unpaid',
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            foreach ($data['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['item_type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);
            }

            return $invoice;
        });

        AuditLogService::log('create', 'invoices', $invoice->id, null, $invoice->toArray());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Tagihan berhasil dibuat.');
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['tenant', 'room.property', 'items', 'payments', 'contract']);

        return view('invoices.show', ['title' => $invoice->invoice_number, 'invoice' => $invoice]);
    }

    public function cancel(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($request->user()->can('invoices.cancel'), 403);

        if ($invoice->paid_amount > 0) {
            return back()->with('error', 'Tagihan yang sudah memiliki pembayaran tidak dapat dibatalkan.');
        }

        $old = $invoice->toArray();
        $invoice->update(['status' => 'cancelled']);

        AuditLogService::log('cancel_invoice', 'invoices', $invoice->id, $old, $invoice->toArray());

        return back()->with('success', 'Tagihan berhasil dibatalkan.');
    }

    public function print(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['tenant', 'room.property', 'items']);

        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }
}
