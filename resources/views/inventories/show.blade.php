<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Inventaris', 'url' => route('inventories.index')], ['label' => $inventory->name]]">
    <x-page-header :title="$inventory->name" :description="$inventory->inventory_code">
        <x-slot:actions>
            <x-status-badge :status="$inventory->condition" :colors="\App\Models\Inventory::CONDITION_COLORS" />
            <x-status-badge :status="$inventory->status" :colors="\App\Models\Inventory::STATUS_COLORS" />
            @can('inventories.update')
                <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-3">
        <div class="card">
            <div class="card-header"><h3>Detail</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Kategori</span><span>{{ $inventory->category }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Kamar</span><span>{{ $inventory->room->room_number ?? '-' }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">No. Seri</span><span>{{ $inventory->serial_number ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Harga Beli</span><span>{{ $inventory->purchase_price ? 'Rp '.number_format($inventory->purchase_price,0,',','.') : '-' }}</span></div>
            </div>
        </div>
        <div class="card" style="grid-column: span 2;">
            <div class="card-header"><h3>Riwayat</h3></div>
            <div class="card-body">
                @forelse($inventory->histories as $history)
                    <div class="widget-item" style="padding-left:0;padding-right:0;">
                        <div class="widget-item-icon badge-primary" style="background:var(--color-primary-soft);color:var(--color-primary);"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div class="widget-item-body">
                            <div class="widget-item-title">{{ ucfirst(str_replace('_', ' ', $history->event_type)) }}</div>
                            <div class="widget-item-desc">
                                @if($history->from_value){{ $history->from_value }} → @endif{{ $history->to_value }}
                                — {{ $history->creator->name ?? 'System' }}
                            </div>
                            <div class="text-xs text-muted mt-8">{{ $history->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-sm">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
