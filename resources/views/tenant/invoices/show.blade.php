<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header :title="$invoice->invoice_number">
        <x-slot:actions>
            <x-status-badge :status="$invoice->status" :colors="\App\Models\Invoice::STATUS_COLORS" />
            @if($invoice->remaining_amount > 0)
                <a href="{{ route('tenant.payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-primary"><i class="fa-solid fa-money-check-dollar"></i> Bayar Sekarang</a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-24">
        <div class="card-header"><h3>Item Tagihan</h3></div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Deskripsi</th><th class="text-right">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
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
                <div class="doc-totals-row grand"><span>Total</span><span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Riwayat Pembayaran</h3></div>
        @if($invoice->payments->isEmpty())
            <div class="card-body"><p class="text-muted text-sm">Belum ada pembayaran.</p></div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Tanggal</th><th class="text-right">Jumlah</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$payment->status" :colors="\App\Models\Payment::STATUS_COLORS" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
