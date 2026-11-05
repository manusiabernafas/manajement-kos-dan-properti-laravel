<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Properti', 'url' => route('properties.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Properti" description="Lengkapi informasi properti baru" />

    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('properties.store') }}" method="POST">
                @csrf
                @include('properties._form')
                <div class="form-actions">
                    <a href="{{ route('properties.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Properti</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
