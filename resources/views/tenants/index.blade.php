<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Penghuni']]">
    <x-page-header title="Penghuni" description="Kelola data seluruh penghuni kost">
        <x-slot:actions>
            @can('tenants.create')
                <a href="{{ route('tenants.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Penghuni</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode, telepon...">
                </div>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                    <option value="former" @selected(request('status') === 'former')>Mantan</option>
                </select>
            </div>
        </form>

        @if($tenants->isEmpty())
            <x-empty-state icon="fa-solid fa-users" title="Belum ada penghuni" description="Tambahkan penghuni pertama untuk mulai mengelola data.">
                <x-slot:action>
                    @can('tenants.create')
                        <a href="{{ route('tenants.create') }}" class="btn btn-primary">+ Tambah Penghuni</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Penghuni</th>
                            <th>Telepon</th>
                            <th>Kamar</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-12">
                                        <img src="{{ $tenant->photo ? asset('storage/'.$tenant->photo) : 'https://ui-avatars.com/api/?name='.urlencode($tenant->full_name).'&background=5965F2&color=fff' }}" class="avatar" style="width:36px;height:36px;">
                                        <div>
                                            <div class="table-cell-primary">{{ $tenant->full_name }}</div>
                                            <div class="table-cell-muted">{{ $tenant->tenant_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $tenant->phone }}</td>
                                <td>{{ $tenant->activeContract?->room?->room_number ?? '-' }}</td>
                                <td><x-status-badge :status="$tenant->status" :colors="['active' => 'success', 'inactive' => 'muted', 'former' => 'danger']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif', 'former' => 'Mantan']" /></td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('tenants.show', $tenant) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Detail</a>
                                            @can('tenants.update')
                                                <a href="{{ route('tenants.edit', $tenant) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                            @endcan
                                            @can('tenants.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('tenants.destroy', $tenant)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $tenants->links() }}
        @endif
    </div>
</x-layouts.app>
