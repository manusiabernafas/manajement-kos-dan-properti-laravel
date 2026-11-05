<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Gedung']]">
    <x-page-header title="Gedung" description="Kelola gedung pada setiap properti">
        <x-slot:actions>
            @can('buildings.create')
                <a href="{{ route('buildings.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Gedung</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode...">
                </div>
                <select name="property_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Properti</option>
                    @foreach($properties as $id => $name)
                        <option value="{{ $id }}" @selected(request('property_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($buildings->isEmpty())
            <x-empty-state icon="fa-solid fa-building" title="Belum ada gedung" description="Tambahkan gedung pertama untuk properti Anda.">
                <x-slot:action>
                    @can('buildings.create')
                        <a href="{{ route('buildings.create') }}" class="btn btn-primary">+ Tambah Gedung</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Gedung</th>
                            <th>Properti</th>
                            <th class="text-center">Lantai</th>
                            <th class="text-center">Kamar</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($buildings as $building)
                            <tr>
                                <td>
                                    <div class="table-cell-primary">{{ $building->name }}</div>
                                    <div class="table-cell-muted">{{ $building->code }}</div>
                                </td>
                                <td>{{ $building->property->name }}</td>
                                <td class="text-center">{{ $building->floors_count }}</td>
                                <td class="text-center">{{ $building->rooms_count }}</td>
                                <td><x-status-badge :status="$building->status" :colors="['active' => 'success', 'inactive' => 'muted']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif']" /></td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('buildings.show', $building) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Detail</a>
                                            @can('buildings.update')
                                                <a href="{{ route('buildings.edit', $building) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                            @endcan
                                            @can('buildings.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('buildings.destroy', $building)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $buildings->links() }}
        @endif
    </div>
</x-layouts.app>
