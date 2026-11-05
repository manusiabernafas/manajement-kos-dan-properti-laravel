<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Profil Saya']]">
    <x-page-header title="Profil Saya" description="Kelola informasi akun Anda" />

    <div class="grid grid-cols-3">
        <div class="card" style="grid-column: span 1;">
            <div class="card-body flex flex-col items-center" style="text-align:center;">
                <img src="{{ auth()->user()->avatar_url }}" class="avatar" style="width:88px;height:88px;margin-bottom:14px;" alt="">
                <h3 class="font-semibold">{{ auth()->user()->name }}</h3>
                <p class="text-muted text-sm mt-8">{{ auth()->user()->getRoleNames()->first() ?? '-' }}</p>
                <div class="mt-16 w-full">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display:none" onchange="this.form.submit()">
                        <label for="avatar-input" class="btn btn-outline btn-block" style="cursor:pointer;">
                            <i class="fa-solid fa-camera"></i> Ganti Foto
                        </label>
                    </form>
                </div>
            </div>
        </div>

        <div class="card" style="grid-column: span 2;">
            <div class="card-header"><h3>Informasi Akun</h3></div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <x-input label="Nama Lengkap" name="name" value="{{ auth()->user()->name }}" required />
                        <x-input label="Email" name="email" type="email" value="{{ auth()->user()->email }}" required />
                    </div>
                    <x-input label="Nomor Telepon" name="phone" value="{{ auth()->user()->phone }}" />
                    <div class="form-actions">
                        <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
