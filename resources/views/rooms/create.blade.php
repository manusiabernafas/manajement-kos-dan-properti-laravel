<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Kamar', 'url' => route('rooms.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Kamar" />
    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf
                @include('rooms._form')
                <div class="form-actions">
                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Kamar</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
