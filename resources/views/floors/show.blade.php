<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Lantai', 'url' => route('floors.index')], ['label' => $title]]">
    <x-page-header :title="$title" :description="$floor->building->property->name.' — '.$floor->building->name">
        <x-slot:actions>
            @can('floors.update')
                <a href="{{ route('floors.edit', $floor) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
            @can('rooms.create')
                <a href="{{ route('rooms.create', ['floor_id' => $floor->id]) }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Kamar</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header"><h3>Kamar</h3></div>
        <div class="card-body">
            @if($floor->rooms->isEmpty())
                <x-empty-state icon="fa-solid fa-door-open" title="Belum ada kamar" description="Tambahkan kamar untuk lantai ini." />
            @else
                <div class="room-grid">
                    @foreach($floor->rooms as $room)
                        <a href="{{ route('rooms.show', $room) }}" class="room-tile status-{{ $room->status }}">
                            <div class="room-tile-number">{{ $room->room_number }}</div>
                            <div class="room-tile-type">{{ $room->roomType->name }}</div>
                            <div class="room-tile-price">Rp {{ number_format($room->price, 0, ',', '.') }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
