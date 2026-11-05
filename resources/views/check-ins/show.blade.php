<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Check-in', 'url' => route('check-ins.index')], ['label' => $checkIn->tenant->full_name]]">
    <x-page-header :title="'Check-in — '.$checkIn->tenant->full_name" :description="$checkIn->room->room_number" />

    <div class="grid grid-cols-2">
        <div class="card">
            <div class="card-header"><h3>Informasi Check-in</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Tanggal Check-in</span><span>{{ $checkIn->check_in_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Harga Bulanan</span><span>Rp {{ number_format($checkIn->monthly_price, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Deposit</span><span>Rp {{ number_format($checkIn->deposit, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Tanggal Billing</span><span>{{ $checkIn->billing_date->format('d M Y') }}</span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Kontrak Terkait</h3></div>
            <div class="card-body">
                <p class="font-semibold">{{ $checkIn->contract->contract_number }}</p>
                <p class="text-muted text-sm mt-8">{{ $checkIn->contract->start_date->format('d M Y') }} — {{ $checkIn->contract->end_date->format('d M Y') }}</p>
                <a href="{{ route('contracts.show', $checkIn->contract) }}" class="btn btn-outline btn-sm mt-16">Lihat Kontrak</a>
            </div>
        </div>
    </div>
</x-layouts.app>
