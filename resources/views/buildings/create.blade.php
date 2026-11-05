<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Gedung', 'url' => route('buildings.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Gedung" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('buildings.store') }}" method="POST">
                @csrf
                @include('buildings._form')
                <div class="form-actions">
                    <a href="{{ route('buildings.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Gedung</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
