<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Roles & Permissions', 'url' => route('roles.index')], ['label' => 'Edit']]">
    <x-page-header title="Edit Role" :description="$role->name" />

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-24" style="max-width: 500px;">
            <div class="card-body">
                <x-input label="Nama Role" name="name" :value="$role->name" required />
            </div>
        </div>

        <div class="form-section-title">Permissions</div>
        @include('roles._permissions', ['checkedIds' => $rolePermissionIds])

        <div class="form-actions mt-24">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Batal</a>
            <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
        </div>
    </form>
</x-layouts.app>
