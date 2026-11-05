<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Tipe Kamar']]">
    <x-page-header title="Tipe Kamar" description="Kelola tipe dan harga kamar">
        <x-slot:actions>
            @can('room-types.create')
                <a href="{{ route('room-types.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Tipe Kamar</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama tipe...">
                </div>
                <select name="property_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Properti</option>
                    @foreach($properties as $id => $name)
                        <option value="{{ $id }}" @selected(request('property_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($roomTypes->isEmpty())
            <x-empty-state icon="fa-solid fa-bed" title="Belum ada tipe kamar" description="Tambahkan tipe kamar untuk mulai menentukan harga.">
                <x-slot:action>
                    @can('room-types.create')
                        <a href="{{ route('room-types.create') }}" class="btn btn-primary">+ Tambah Tipe Kamar</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipe Kamar</th>
                            <th>Properti</th>
                            <th class="text-right">Harga/Bulan</th>
                            <th class="text-center">Kapasitas</th>
                            <th class="text-center">Kamar</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roomTypes as $roomType)
                            <tr>
                                <td>
                                    <div class="table-cell-primary">{{ $roomType->name }}</div>
                                    <div class="table-cell-muted">{{ $roomType->code }}</div>
                                </td>
                                <td>{{ $roomType->property->name }}</td>
                                <td class="text-right">Rp {{ number_format($roomType->monthly_price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $roomType->capacity }} org</td>
                                <td class="text-center">{{ $roomType->rooms_count }}</td>
                                <td><x-status-badge :status="$roomType->status" :colors="['active' => 'success', 'inactive' => 'muted']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif']" /></td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('room-types.show', $roomType) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Detail</a>
                                            @can('room-types.update')
                                                <a href="{{ route('room-types.edit', $roomType) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                            @endcan
                                            @can('room-types.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('room-types.destroy', $roomType)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $roomTypes->links() }}
        @endif
    </div>
</x-layouts.app>
