<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Kamar']]">
    <x-page-header title="Kamar" description="Visualisasi status seluruh kamar">
        <x-slot:actions>
            @can('rooms.create')
                <a href="{{ route('rooms.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Kamar</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-24">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor kamar...">
                </div>
                <select name="property_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Properti</option>
                    @foreach($properties as $id => $name)
                        <option value="{{ $id }}" @selected(request('property_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="available" @selected(request('status') === 'available')>Available</option>
                    <option value="occupied" @selected(request('status') === 'occupied')>Occupied</option>
                    <option value="reserved" @selected(request('status') === 'reserved')>Reserved</option>
                    <option value="maintenance" @selected(request('status') === 'maintenance')>Maintenance</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="room-legend">
                <span class="room-legend-item"><span class="room-legend-dot" style="background:var(--color-success)"></span> Available</span>
                <span class="room-legend-item"><span class="room-legend-dot" style="background:var(--color-info)"></span> Occupied</span>
                <span class="room-legend-item"><span class="room-legend-dot" style="background:var(--color-warning)"></span> Reserved</span>
                <span class="room-legend-item"><span class="room-legend-dot" style="background:var(--color-danger)"></span> Maintenance</span>
                <span class="room-legend-item"><span class="room-legend-dot" style="background:var(--color-muted)"></span> Inactive</span>
            </div>
        </form>
    </div>

    @if($rooms->isEmpty())
        <x-empty-state icon="fa-solid fa-door-open" title="Belum ada kamar" description="Tambahkan kamar pertama untuk mulai mengelola properti.">
            <x-slot:action>
                @can('rooms.create')
                    <a href="{{ route('rooms.create') }}" class="btn btn-primary">+ Tambah Kamar</a>
                @endcan
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="room-grid">
            @foreach($rooms as $room)
                <a href="{{ route('rooms.show', $room) }}" class="room-tile status-{{ $room->status }}">
                    <div class="room-tile-number">{{ $room->room_number }}</div>
                    <div class="room-tile-type">{{ $room->roomType->name }}</div>
                    @if($room->activeContract?->tenant)
                        <div class="room-tile-tenant"><i class="fa-solid fa-user text-muted"></i> {{ $room->activeContract->tenant->full_name }}</div>
                    @endif
                    <div class="room-tile-price">Rp {{ number_format($room->price, 0, ',', '.') }}</div>
                    <div class="mt-8"><x-badge :variant="\App\Models\Room::STATUS_COLORS[$room->status]">{{ ucfirst($room->status) }}</x-badge></div>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.app>
