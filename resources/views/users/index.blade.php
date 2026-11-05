<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Users']]">
    <x-page-header title="Users" description="Kelola akun pengguna sistem">
        <x-slot:actions>
            @can('users.create')
                <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah User</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email...">
                </div>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
        </form>

        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>User</th><th>Role</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-12">
                                    <img src="{{ $user->avatar_url }}" class="avatar" style="width:36px;height:36px;">
                                    <div>
                                        <div class="table-cell-primary">{{ $user->name }}</div>
                                        <div class="table-cell-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                            <td><x-status-badge :status="$user->status" :colors="['active' => 'success', 'inactive' => 'muted']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif']" /></td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                    <div class="dropdown-menu">
                                        @can('users.update')
                                            <a href="{{ route('users.edit', $user) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                        @endcan
                                        @can('users.delete')
                                            <div class="dropdown-divider"></div>
                                            <x-delete-form :action="route('users.destroy', $user)" />
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</x-layouts.app>
