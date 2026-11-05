<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Lantai']]">
    <x-page-header title="Lantai" description="Kelola lantai pada setiap gedung">
        <x-slot:actions>
            @can('floors.create')
                <a href="{{ route('floors.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Lantai</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <select name="building_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Gedung</option>
                    @foreach($buildings as $b)
                        <option value="{{ $b->id }}" @selected(request('building_id') == $b->id)>{{ $b->property->name }} — {{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($floors->isEmpty())
            <x-empty-state icon="fa-solid fa-layer-group" title="Belum ada lantai" description="Tambahkan lantai pertama untuk gedung Anda.">
                <x-slot:action>
                    @can('floors.create')
                        <a href="{{ route('floors.create') }}" class="btn btn-primary">+ Tambah Lantai</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Lantai</th>
                            <th>Gedung</th>
                            <th>Properti</th>
                            <th class="text-center">Kamar</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($floors as $floor)
                            <tr>
                                <td class="table-cell-primary">{{ $floor->name ?? 'Lantai '.$floor->floor_number }}</td>
                                <td>{{ $floor->building->name }}</td>
                                <td>{{ $floor->building->property->name }}</td>
                                <td class="text-center">{{ $floor->rooms_count }}</td>
                                <td><x-status-badge :status="$floor->status" :colors="['active' => 'success', 'inactive' => 'muted']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif']" /></td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('floors.show', $floor) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Detail</a>
                                            @can('floors.update')
                                                <a href="{{ route('floors.edit', $floor) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                            @endcan
                                            @can('floors.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('floors.destroy', $floor)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $floors->links() }}
        @endif
    </div>
</x-layouts.app>
