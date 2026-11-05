<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Roles & Permissions', 'url' => route('roles.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Role" />

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <div class="card mb-24" style="max-width: 500px;">
            <div class="card-body">
                <x-input label="Nama Role" name="name" required />
            </div>
        </div>

        <div class="form-section-title">Permissions</div>
        @include('roles._permissions', ['checkedIds' => []])

        <div class="form-actions mt-24">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Batal</a>
            <x-button type="submit" variant="primary">Simpan Role</x-button>
        </div>
    </form>
</x-layouts.app>
