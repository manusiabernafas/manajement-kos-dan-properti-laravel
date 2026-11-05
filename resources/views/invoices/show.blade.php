<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Tagihan', 'url' => route('invoices.index')], ['label' => $invoice->invoice_number]]">
    <x-page-header :title="$invoice->invoice_number" :description="$invoice->tenant->full_name">
        <x-slot:actions>
            <x-status-badge :status="$invoice->status" :colors="\App\Models\Invoice::STATUS_COLORS" />
            <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-outline"><i class="fa-solid fa-print"></i> Cetak</a>
            @if($invoice->remaining_amount > 0 && $invoice->status !== 'cancelled')
                @can('payments.create')
                    <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-primary"><i class="fa-solid fa-money-check-dollar"></i> Catat Pembayaran</a>
                @endcan
            @endif
            @if($invoice->paid_amount == 0 && $invoice->status !== 'cancelled')
                @can('invoices.cancel')
                    <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" data-confirm-submit data-confirm-message="Batalkan tagihan ini?">
                        @csrf
                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Batalkan</button>
                    </form>
                @endcan
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-3 mb-24">
        <x-stat-card label="Total Tagihan" :value="'Rp '.number_format($invoice->total, 0, ',', '.')" icon="fa-solid fa-file-invoice-dollar" variant="primary" />
        <x-stat-card label="Sudah Dibayar" :value="'Rp '.number_format($invoice->paid_amount, 0, ',', '.')" icon="fa-solid fa-circle-check" variant="success" />
        <x-stat-card label="Sisa Tagihan" :value="'Rp '.number_format($invoice->remaining_amount, 0, ',', '.')" icon="fa-solid fa-hourglass-half" variant="warning" />
    </div>

    <div class="card mb-24">
        <div class="card-header"><h3>Item Tagihan</h3></div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Deskripsi</th><th>Jenis</th><th class="text-right">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
                            <td>{{ ucfirst($item->item_type) }}</td>
                            <td class="text-right">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="doc-totals">
                <div class="doc-totals-row"><span>Subtotal</span><span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span></div>
                @if($invoice->discount > 0)<div class="doc-totals-row"><span>Diskon</span><span>- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span></div>@endif
                @if($invoice->penalty > 0)<div class="doc-totals-row"><span>Denda</span><span>Rp {{ number_format($invoice->penalty, 0, ',', '.') }}</span></div>@endif
                @if($invoice->tax > 0)<div class="doc-totals-row"><span>Pajak</span><span>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span></div>@endif
                <div class="doc-totals-row grand"><span>Total</span><span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Riwayat Pembayaran</h3></div>
        @if($invoice->payments->isEmpty())
            <div class="card-body"><p class="text-muted text-sm">Belum ada pembayaran untuk tagihan ini.</p></div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>No. Pembayaran</th><th>Tanggal</th><th class="text-right">Jumlah</th><th>Metode</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td><a href="{{ route('payments.show', $payment) }}">{{ $payment->payment_number }}</a></td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td>{{ strtoupper(str_replace('_',' ',$payment->payment_method)) }}</td>
                                <td><x-status-badge :status="$payment->status" :colors="\App\Models\Payment::STATUS_COLORS" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
