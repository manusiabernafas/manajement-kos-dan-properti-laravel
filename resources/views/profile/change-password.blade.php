<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Ganti Password']]">
    <x-page-header title="Ganti Password" description="Perbarui password akun Anda secara berkala untuk keamanan" />

    <div class="card" style="max-width: 520px;">
        <div class="card-body">
            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <x-input label="Password Saat Ini" name="current_password" type="password" required />
                <x-input label="Password Baru" name="password" type="password" required hint="Minimal 8 karakter" />
                <x-input label="Konfirmasi Password Baru" name="password_confirmation" type="password" required />
                <div class="form-actions">
                    <x-button type="submit" variant="primary">Perbarui Password</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
