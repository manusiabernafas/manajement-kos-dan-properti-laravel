<x-layouts.guest :title="$title">
    <div class="auth-card">
        <div class="auth-brand">
            <span class="auth-brand-icon"><i class="fa-solid fa-building-columns"></i></span>
            <span class="auth-brand-text">KostManager</span>
        </div>

        <h1 class="auth-title">Lupa Password</h1>
        <p class="auth-subtitle">Masukkan email Anda untuk menerima tautan reset password</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <x-input label="Email" name="email" type="email" value="{{ old('email') }}" required autofocus />
            <x-button type="submit" variant="primary" class="btn-block">Kirim Tautan Reset</x-button>
        </form>

        <div class="auth-footer">
            <a href="{{ route('login') }}"><i class="fa-solid fa-arrow-left"></i> Kembali ke login</a>
        </div>
    </div>
</x-layouts.guest>
