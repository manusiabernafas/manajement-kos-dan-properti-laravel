<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Properti', 'url' => route('properties.index')], ['label' => $property->name]]">
    <x-page-header :title="$property->name" :description="$property->code">
        <x-slot:actions>
            @can('properties.update')
                <a href="{{ route('properties.edit', $property) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
            <a href="{{ route('rooms.index', ['property_id' => $property->id]) }}" class="btn btn-primary"><i class="fa-solid fa-door-open"></i> Lihat Kamar</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-4 mb-24">
        <x-stat-card label="Total Gedung" :value="$property->buildings_count" icon="fa-solid fa-building" variant="primary" />
        <x-stat-card label="Total Kamar" :value="$property->rooms_count" icon="fa-solid fa-door-open" variant="info" />
        <x-stat-card label="Kamar Terisi" :value="$roomStats['occupied'] ?? 0" icon="fa-solid fa-user-check" variant="success" />
        <x-stat-card label="Kamar Kosong" :value="$roomStats['available'] ?? 0" icon="fa-solid fa-door-closed" variant="warning" />
    </div>

    <div class="grid grid-cols-3">
        <div class="card" style="grid-column: span 2;">
            <div class="card-header">
                <h3>Struktur Gedung & Lantai</h3>
                @can('buildings.create')
                    <a href="{{ route('buildings.create', ['property_id' => $property->id]) }}" class="btn btn-sm btn-outline"><i class="fa-solid fa-plus"></i> Tambah Gedung</a>
                @endcan
            </div>
            <div class="card-body">
                @forelse($property->buildings as $building)
                    <div class="mb-16" style="padding-bottom:16px; border-bottom: 1px solid var(--color-border);">
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('buildings.show', $building) }}" class="font-semibold">{{ $building->name }}</a>
                                <span class="text-muted text-sm"> ({{ $building->code }})</span>
                            </div>
                            <x-status-badge :status="$building->status" :colors="['active' => 'success', 'inactive' => 'muted']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif']" />
                        </div>
                        <div class="text-sm text-muted mt-8">
                            {{ $building->floors->count() }} lantai
                        </div>
                    </div>
                @empty
                    <x-empty-state icon="fa-solid fa-building" title="Belum ada gedung" description="Tambahkan gedung untuk mulai menyusun struktur properti ini." />
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Informasi</h3></div>
            <div class="card-body">
                <p class="text-sm mb-12"><i class="fa-solid fa-location-dot text-muted"></i> {{ $property->address }}</p>
                @if($property->phone)<p class="text-sm mb-12"><i class="fa-solid fa-phone text-muted"></i> {{ $property->phone }}</p>@endif
                @if($property->email)<p class="text-sm mb-12"><i class="fa-solid fa-envelope text-muted"></i> {{ $property->email }}</p>@endif
                @if($property->bank_name)
                    <div class="mt-16" style="padding-top:16px;border-top:1px solid var(--color-border);">
                        <div class="text-sm font-semibold mb-8">Rekening Pembayaran</div>
                        <p class="text-sm">{{ $property->bank_name }} — {{ $property->bank_account_number }}</p>
                        <p class="text-sm text-muted">a.n. {{ $property->bank_account_name }}</p>
                    </div>
                @endif
                @if($property->google_maps_url)
                    <a href="{{ $property->google_maps_url }}" target="_blank" class="btn btn-outline btn-sm btn-block mt-16"><i class="fa-solid fa-map"></i> Buka di Google Maps</a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
