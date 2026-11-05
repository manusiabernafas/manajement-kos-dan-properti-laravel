<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Gedung', 'url' => route('buildings.index')], ['label' => $building->name]]">
    <x-page-header :title="$building->name" :description="$building->property->name">
        <x-slot:actions>
            @can('buildings.update')
                <a href="{{ route('buildings.edit', $building) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
            @can('floors.create')
                <a href="{{ route('floors.create', ['building_id' => $building->id]) }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Lantai</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header"><h3>Lantai</h3></div>
        <div class="card-body">
            @forelse($building->floors as $floor)
                <div class="mb-16" style="padding-bottom:16px; border-bottom: 1px solid var(--color-border);">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('floors.show', $floor) }}" class="font-semibold">{{ $floor->name ?? 'Lantai '.$floor->floor_number }}</a>
                        <span class="text-muted text-sm">{{ $floor->rooms->count() }} kamar</span>
                    </div>
                </div>
            @empty
                <x-empty-state icon="fa-solid fa-layer-group" title="Belum ada lantai" description="Tambahkan lantai untuk gedung ini." />
            @endforelse
        </div>
    </div>
</x-layouts.app>
