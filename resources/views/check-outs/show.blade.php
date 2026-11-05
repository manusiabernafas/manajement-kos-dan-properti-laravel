<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Check-out', 'url' => route('check-outs.index')], ['label' => $checkOut->tenant->full_name]]">
    <x-page-header :title="'Check-out — '.$checkOut->tenant->full_name" :description="$checkOut->room->room_number" />

    <div class="grid grid-cols-2">
        <div class="card">
            <div class="card-header"><h3>Rincian Settlement</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Deposit Awal</span><span>Rp {{ number_format($checkOut->deposit_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Piutang Belum Lunas</span><span>- Rp {{ number_format($checkOut->outstanding_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Biaya Kerusakan</span><span>- Rp {{ number_format($checkOut->damage_cost, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Denda</span><span>- Rp {{ number_format($checkOut->penalty_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between" style="border-top:1px solid var(--color-border); padding-top:12px; margin-top:6px;">
                    <span class="font-semibold">Refund ke Penghuni</span>
                    <span class="font-semibold">Rp {{ number_format($checkOut->refund_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Informasi</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Tanggal Checkout</span><span>{{ $checkOut->checkout_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Alasan</span><span>{{ $checkOut->reason ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Kontrak</span><span><a href="{{ route('contracts.show', $checkOut->contract) }}">{{ $checkOut->contract->contract_number }}</a></span></div>
                @if($checkOut->notes)
                    <p class="text-sm text-muted mt-16">{{ $checkOut->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
