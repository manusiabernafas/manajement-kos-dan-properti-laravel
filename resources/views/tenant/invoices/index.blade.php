<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Tagihan Saya" />

    <div class="card">
        @if($invoices->isEmpty())
            <x-empty-state icon="fa-solid fa-file-invoice-dollar" title="Belum ada tagihan" />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>No. Invoice</th><th>Jatuh Tempo</th><th class="text-right">Total</th><th class="text-right">Sisa</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td class="table-cell-primary">{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->due_date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$invoice->status" :colors="\App\Models\Invoice::STATUS_COLORS" /></td>
                                <td class="text-right">
                                    <a href="{{ route('tenant.invoices.show', $invoice) }}" class="btn btn-sm btn-outline">Detail</a>
                                    @if($invoice->remaining_amount > 0)
                                        <a href="{{ route('tenant.payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-sm btn-primary">Bayar</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $invoices->links() }}
        @endif
    </div>
</x-layouts.app>
