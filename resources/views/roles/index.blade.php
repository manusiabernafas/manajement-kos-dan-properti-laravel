<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Roles & Permissions']]">
    <x-page-header title="Roles & Permissions" description="Kelola role dan hak akses pengguna">
        <x-slot:actions>
            @can('roles.create')
                <a href="{{ route('roles.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Role</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Role</th><th class="text-center">Users</th><th class="text-center">Permissions</th><th></th></tr></thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td class="table-cell-primary">{{ $role->name }}</td>
                            <td class="text-center">{{ $role->users_count }}</td>
                            <td class="text-center">{{ $role->permissions_count }}</td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                    <div class="dropdown-menu">
                                        @can('roles.update')
                                            <a href="{{ route('roles.edit', $role) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                        @endcan
                                        @can('roles.delete')
                                            @if(!in_array($role->name, ['Super Admin', 'Owner', 'Tenant']))
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('roles.destroy', $role)" />
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
