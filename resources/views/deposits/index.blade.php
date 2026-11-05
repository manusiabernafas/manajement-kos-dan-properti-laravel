<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Deposit']]">
    <x-page-header title="Deposit" description="Kelola deposit penghuni" />

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="held" @selected(request('status') === 'held')>Held</option>
                    <option value="partially_refunded" @selected(request('status') === 'partially_refunded')>Partially Refunded</option>
                    <option value="refunded" @selected(request('status') === 'refunded')>Refunded</option>
                    <option value="forfeited" @selected(request('status') === 'forfeited')>Forfeited</option>
                </select>
            </div>
        </form>

        @if($deposits->isEmpty())
            <x-empty-state icon="fa-solid fa-piggy-bank" title="Belum ada deposit" description="Deposit akan otomatis dibuat saat proses check-in." />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Penghuni</th><th>Kamar</th><th class="text-right">Jumlah</th><th class="text-right">Refund</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($deposits as $deposit)
                            <tr>
                                <td class="table-cell-primary">{{ $deposit->tenant->full_name }}</td>
                                <td>{{ $deposit->room->room_number }}</td>
                                <td class="text-right">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($deposit->refund_amount ?? 0, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$deposit->status" :colors="\App\Models\Deposit::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('deposits.show', $deposit) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $deposits->links() }}
        @endif
    </div>
</x-layouts.app>
