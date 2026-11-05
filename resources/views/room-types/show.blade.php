<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Tipe Kamar', 'url' => route('room-types.index')], ['label' => $roomType->name]]">
    <x-page-header :title="$roomType->name" :description="$roomType->property->name">
        <x-slot:actions>
            @can('room-types.update')
                <a href="{{ route('room-types.edit', $roomType) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-3">
        <div class="card">
            <div class="card-header"><h3>Harga</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Bulanan</span><span class="font-semibold">Rp {{ number_format($roomType->monthly_price, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Mingguan</span><span class="font-semibold">{{ $roomType->weekly_price ? 'Rp '.number_format($roomType->weekly_price, 0, ',', '.') : '-' }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Harian</span><span class="font-semibold">{{ $roomType->daily_price ? 'Rp '.number_format($roomType->daily_price, 0, ',', '.') : '-' }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Deposit</span><span class="font-semibold">Rp {{ number_format($roomType->deposit_amount, 0, ',', '.') }}</span></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Fasilitas</h3></div>
            <div class="card-body">
                @forelse($roomType->facilities as $facility)
                    <span class="badge badge-muted mb-8" style="margin-right:6px;">{{ $facility->name }}</span>
                @empty
                    <p class="text-muted text-sm">Belum ada fasilitas.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Kamar Menggunakan Tipe Ini</h3></div>
            <div class="card-body">
                <p class="stat-card-value">{{ $roomType->rooms->count() }}</p>
                <p class="text-muted text-sm">kamar</p>
            </div>
        </div>
    </div>
</x-layouts.app>
