<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Inventaris']]">
    <x-page-header title="Inventaris" description="Kelola aset dan inventaris properti">
        <x-slot:actions>
            @can('inventories.create')
                <a href="{{ route('inventories.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Inventaris</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/kode...">
                </div>
                <select name="property_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Properti</option>
                    @foreach($properties as $id => $name)
                        <option value="{{ $id }}" @selected(request('property_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($inventories->isEmpty())
            <x-empty-state icon="fa-solid fa-boxes-stacked" title="Belum ada inventaris" description="Tambahkan aset pertama untuk properti Anda.">
                <x-slot:action>
                    @can('inventories.create')
                        <a href="{{ route('inventories.create') }}" class="btn btn-primary">+ Tambah Inventaris</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Kode</th><th>Nama</th><th>Kamar</th><th>Kondisi</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($inventories as $inventory)
                            <tr>
                                <td class="table-cell-primary">{{ $inventory->inventory_code }}</td>
                                <td>{{ $inventory->name }}</td>
                                <td>{{ $inventory->room->room_number ?? '-' }}</td>
                                <td><x-status-badge :status="$inventory->condition" :colors="\App\Models\Inventory::CONDITION_COLORS" /></td>
                                <td><x-status-badge :status="$inventory->status" :colors="\App\Models\Inventory::STATUS_COLORS" /></td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('inventories.show', $inventory) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Detail</a>
                                            @can('inventories.update')
                                                <a href="{{ route('inventories.edit', $inventory) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                            @endcan
                                            @can('inventories.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('inventories.destroy', $inventory)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $inventories->links() }}
        @endif
    </div>
</x-layouts.app>
