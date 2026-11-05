<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Kontrak Saya" />

    @if(!$tenant->activeContract)
        <x-empty-state icon="fa-solid fa-file-signature" title="Belum ada kontrak aktif" />
    @else
        @php $contract = $tenant->activeContract; @endphp
        <div class="card" style="max-width: 700px;">
            <div class="card-header">
                <h3>{{ $contract->contract_number }}</h3>
                <x-status-badge :status="$contract->status" :colors="\App\Models\Contract::STATUS_COLORS" />
            </div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Mulai</span><span>{{ $contract->start_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Berakhir</span><span>{{ $contract->end_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Harga Bulanan</span><span>Rp {{ number_format($contract->monthly_price, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Tanggal Billing</span><span>Setiap tanggal {{ $contract->billing_day }}</span></div>
                @if($contract->terms)
                    <div class="mt-24" style="padding-top:16px;border-top:1px solid var(--color-border);">
                        <div class="text-sm font-semibold mb-8">Syarat & Ketentuan</div>
                        <p class="text-sm" style="white-space: pre-line;">{{ $contract->terms }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-layouts.app>
