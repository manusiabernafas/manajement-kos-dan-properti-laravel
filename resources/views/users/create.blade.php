<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Users', 'url' => route('users.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah User" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <x-input label="Nama Lengkap" name="name" required />
                <div class="form-row">
                    <x-input label="Email" name="email" type="email" required />
                    <x-input label="Telepon" name="phone" />
                </div>
                <x-input label="Password" name="password" type="password" required hint="Minimal 8 karakter" />
                <div class="form-row">
                    <x-select label="Role" name="role" required :options="$roles" />
                    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" value="active" />
                </div>
                <div class="form-actions">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan User</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
