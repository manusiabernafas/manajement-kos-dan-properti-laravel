<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pembayaran']]">
    <x-page-header title="Pembayaran" description="Verifikasi dan kelola pembayaran penghuni">
        <x-slot:actions>
            @can('payments.create')
                <a href="{{ route('payments.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Catat Pembayaran</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. pembayaran...">
                </div>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="verified" @selected(request('status') === 'verified')>Verified</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
            </div>
        </form>

        @if($payments->isEmpty())
            <x-empty-state icon="fa-solid fa-money-check-dollar" title="Belum ada pembayaran" description="Catat pembayaran pertama untuk tagihan penghuni.">
                <x-slot:action>
                    @can('payments.create')
                        <a href="{{ route('payments.create') }}" class="btn btn-primary">+ Catat Pembayaran</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>No. Pembayaran</th><th>Penghuni</th><th>Invoice</th><th>Tanggal</th><th class="text-right">Jumlah</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td class="table-cell-primary">{{ $payment->payment_number }}</td>
                                <td>{{ $payment->tenant->full_name }}</td>
                                <td>{{ $payment->invoice->invoice_number }}</td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$payment->status" :colors="\App\Models\Payment::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
        @endif
    </div>
</x-layouts.app>
