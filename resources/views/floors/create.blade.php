<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Lantai', 'url' => route('floors.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Lantai" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('floors.store') }}" method="POST">
                @csrf
                @include('floors._form')
                <div class="form-actions">
                    <a href="{{ route('floors.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Lantai</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
