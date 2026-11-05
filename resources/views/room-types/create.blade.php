<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Tipe Kamar', 'url' => route('room-types.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Tipe Kamar" />
    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('room-types.store') }}" method="POST">
                @csrf
                @include('room-types._form')
                <div class="form-actions">
                    <a href="{{ route('room-types.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Tipe Kamar</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
