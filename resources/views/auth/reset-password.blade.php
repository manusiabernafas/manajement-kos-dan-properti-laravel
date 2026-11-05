<x-layouts.guest :title="$title">
    <div class="auth-card">
        <div class="auth-brand">
            <span class="auth-brand-icon"><i class="fa-solid fa-building-columns"></i></span>
            <span class="auth-brand-text">KostManager</span>
        </div>

        <h1 class="auth-title">Reset Password</h1>
        <p class="auth-subtitle">Buat password baru untuk akun Anda</p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <x-input label="Email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus />
            <x-input label="Password Baru" name="password" type="password" required />
            <x-input label="Konfirmasi Password" name="password_confirmation" type="password" required />
            <x-button type="submit" variant="primary" class="btn-block">Reset Password</x-button>
        </form>
    </div>
</x-layouts.guest>
