<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Pembayaran Saya">
        <x-slot:actions>
            <a href="{{ route('tenant.payments.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Ajukan Pembayaran</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        @if($payments->isEmpty())
            <x-empty-state icon="fa-solid fa-money-check-dollar" title="Belum ada pembayaran" />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>No. Pembayaran</th><th>Invoice</th><th>Tanggal</th><th class="text-right">Jumlah</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td class="table-cell-primary">{{ $payment->payment_number }}</td>
                                <td>{{ $payment->invoice->invoice_number }}</td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$payment->status" :colors="\App\Models\Payment::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('tenant.payments.show', $payment) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
        @endif
    </div>
</x-layouts.app>
