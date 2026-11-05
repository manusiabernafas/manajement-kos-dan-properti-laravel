<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Properti']]">
    <x-page-header title="Properti" description="Kelola seluruh properti kost Anda">
        <x-slot:actions>
            @can('properties.create')
                <a href="{{ route('properties.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Properti</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode, kota...">
                </div>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
        </form>

        @if($properties->isEmpty())
            <x-empty-state icon="fa-solid fa-city" title="Belum ada properti" description="Tambahkan properti pertama untuk mulai mengelola kost Anda.">
                <x-slot:action>
                    @can('properties.create')
                        <a href="{{ route('properties.create') }}" class="btn btn-primary">+ Tambah Properti</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Properti</th>
                            <th>Lokasi</th>
                            <th class="text-center">Gedung</th>
                            <th class="text-center">Kamar</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                            <tr>
                                <td>
                                    <div class="table-cell-primary">{{ $property->name }}</div>
                                    <div class="table-cell-muted">{{ $property->code }}</div>
                                </td>
                                <td>{{ $property->city ?? '-' }}{{ $property->province ? ', '.$property->province : '' }}</td>
                                <td class="text-center">{{ $property->buildings_count }}</td>
                                <td class="text-center">{{ $property->rooms_count }}</td>
                                <td>
                                    <x-status-badge :status="$property->status" :colors="['active' => 'success', 'inactive' => 'muted']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif']" />
                                </td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('properties.show', $property) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Lihat Detail</a>
                                            @can('properties.update')
                                                <a href="{{ route('properties.edit', $property) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                            @endcan
                                            @can('properties.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('properties.destroy', $property)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $properties->links() }}
        @endif
    </div>
</x-layouts.app>
