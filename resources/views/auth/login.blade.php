<x-layouts.guest :title="$title">
    <div class="auth-card">
        <div class="auth-brand">
            <span class="auth-brand-icon"><i class="fa-solid fa-building-columns"></i></span>
            <span class="auth-brand-text">KostManager</span>
        </div>

        <h1 class="auth-title">Selamat Datang Kembali</h1>
        <p class="auth-subtitle">Masuk untuk mengelola properti kost Anda</p>

        @error('email')
            <x-alert variant="danger" class="mb-16">{{ $message }}</x-alert>
        @enderror

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <x-input label="Email" name="email" type="email" value="{{ old('email') }}" required autofocus />
            <x-input label="Password" name="password" type="password" required />

            <div class="flex items-center justify-between mb-16">
                <label class="checkbox-wrap">
                    <input type="checkbox" name="remember">
                    <span>Ingat saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm" style="color: var(--color-primary); font-weight: 600;">Lupa password?</a>
            </div>

            <x-button type="submit" variant="primary" class="btn-block">Masuk</x-button>
        </form>

        <div class="demo-accounts">
            <strong>Akun Demo:</strong><br>
            Super Admin: admin@kostmanager.test<br>
            Owner: owner@kostmanager.test<br>
            Finance: finance@kostmanager.test<br>
            Staff: staff@kostmanager.test<br>
            Tenant: tenant@kostmanager.test<br>
            Password untuk semua: <strong>password</strong>
        </div>
    </div>
</x-layouts.guest>
