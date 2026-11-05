<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Inventaris', 'url' => route('inventories.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Inventaris" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('inventories.store') }}" method="POST">
                @csrf
                @include('inventories._form')
                <div class="form-actions">
                    <a href="{{ route('inventories.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Inventaris</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
