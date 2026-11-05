<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantInvoiceController extends PortalController
{
    public function index(Request $request): View
    {
        $tenant = $this->tenant($request);

        $invoices = $tenant->invoices()->orderByDesc('issue_date')->paginate(10);

        return view('tenant.invoices.index', ['title' => 'Tagihan Saya', 'invoices' => $invoices]);
    }

    public function show(Request $request, Invoice $invoice): View
    {
        $tenant = $this->tenant($request);
        abort_unless($invoice->tenant_id === $tenant->id, 403);

        $invoice->load(['items', 'payments', 'room']);

        return view('tenant.invoices.show', ['title' => $invoice->invoice_number, 'invoice' => $invoice]);
    }
}
