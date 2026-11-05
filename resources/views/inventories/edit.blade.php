<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Inventaris', 'url' => route('inventories.index')], ['label' => $inventory->name, 'url' => route('inventories.show', $inventory)], ['label' => 'Edit']]">
    <x-page-header title="Edit Inventaris" :description="$inventory->name" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('inventories.update', $inventory) }}" method="POST">
                @csrf
                @method('PUT')
                @include('inventories._form')
                <div class="form-actions">
                    <a href="{{ route('inventories.show', $inventory) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
