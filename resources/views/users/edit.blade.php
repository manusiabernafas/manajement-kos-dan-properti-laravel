<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Users', 'url' => route('users.index')], ['label' => 'Edit']]">
    <x-page-header title="Edit User" :description="$targetUser->name" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('users.update', $targetUser) }}" method="POST">
                @csrf
                @method('PUT')
                <x-input label="Nama Lengkap" name="name" :value="$targetUser->name" required />
                <div class="form-row">
                    <x-input label="Email" name="email" type="email" :value="$targetUser->email" required />
                    <x-input label="Telepon" name="phone" :value="$targetUser->phone" />
                </div>
                <x-input label="Password Baru" name="password" type="password" hint="Kosongkan jika tidak ingin mengubah password" />
                <div class="form-row">
                    <x-select label="Role" name="role" required :options="$roles" :value="$targetUser->roles->first()?->name" />
                    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$targetUser->status" />
                </div>
                <div class="form-actions">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
