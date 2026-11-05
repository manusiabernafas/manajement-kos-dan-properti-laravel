<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Kontrak', 'url' => route('contracts.index')], ['label' => $contract->contract_number]]">
    <x-page-header :title="$contract->contract_number" :description="$contract->tenant->full_name">
        <x-slot:actions>
            <x-status-badge :status="$contract->status" :colors="\App\Models\Contract::STATUS_COLORS" />
            @can('contracts.update')
                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
            <a href="{{ route('contracts.print', $contract) }}" target="_blank" class="btn btn-outline"><i class="fa-solid fa-print"></i> Cetak PDF</a>
            @if($contract->status === 'active')
                @can('check-outs.create')
                    <a href="{{ route('check-outs.create', ['contract_id' => $contract->id]) }}" class="btn btn-danger"><i class="fa-solid fa-right-from-bracket"></i> Check-out</a>
                @endcan
            @endif
        </x-slot:actions>
    </x-page-header>

    @if($contract->status === 'active' && $contract->daysUntilExpiry() <= 30 && $contract->daysUntilExpiry() >= 0)
        <x-alert variant="warning" class="mb-24">Kontrak ini akan berakhir dalam {{ $contract->daysUntilExpiry() }} hari.</x-alert>
    @endif

    <div class="grid grid-cols-3">
        <div class="card">
            <div class="card-header"><h3>Detail Kontrak</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Mulai</span><span>{{ $contract->start_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Berakhir</span><span>{{ $contract->end_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Harga Bulanan</span><span>Rp {{ number_format($contract->monthly_price, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Deposit</span><span>Rp {{ number_format($contract->deposit, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Tanggal Billing</span><span>Setiap tgl {{ $contract->billing_day }}</span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Penghuni</h3></div>
            <div class="card-body">
                <p class="font-semibold">{{ $contract->tenant->full_name }}</p>
                <p class="text-muted text-sm mt-8">{{ $contract->tenant->phone }}</p>
                <a href="{{ route('tenants.show', $contract->tenant) }}" class="btn btn-outline btn-sm mt-16">Lihat Profil</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Kamar</h3></div>
            <div class="card-body">
                <p class="font-semibold">{{ $contract->room->room_number }}</p>
                <p class="text-muted text-sm mt-8">{{ $contract->room->property->name }}</p>
                <a href="{{ route('rooms.show', $contract->room) }}" class="btn btn-outline btn-sm mt-16">Lihat Kamar</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 mt-24">
        <div class="card">
            <div class="card-header"><h3>Tagihan</h3></div>
            @if($contract->invoices->isEmpty())
                <div class="card-body"><p class="text-muted text-sm">Belum ada tagihan.</p></div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>No. Invoice</th><th class="text-right">Total</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($contract->invoices as $invoice)
                                <tr>
                                    <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                                    <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                    <td><x-status-badge :status="$invoice->status" :colors="\App\Models\Invoice::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="card">
            <div class="card-header"><h3>Deposit</h3></div>
            @if($contract->deposits->isEmpty())
                <div class="card-body"><p class="text-muted text-sm">Belum ada deposit.</p></div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th class="text-right">Jumlah</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($contract->deposits as $deposit)
                                <tr>
                                    <td class="text-right">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</td>
                                    <td><x-status-badge :status="$deposit->status" :colors="\App\Models\Deposit::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if($contract->terms)
        <div class="card mt-24">
            <div class="card-header"><h3>Syarat & Ketentuan</h3></div>
            <div class="card-body"><p class="text-sm" style="white-space: pre-line;">{{ $contract->terms }}</p></div>
        </div>
    @endif
</x-layouts.app>
