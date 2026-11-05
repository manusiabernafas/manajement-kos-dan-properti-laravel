<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Gedung', 'url' => route('buildings.index')], ['label' => $building->name, 'url' => route('buildings.show', $building)], ['label' => 'Edit']]">
    <x-page-header title="Edit Gedung" :description="$building->name" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('buildings.update', $building) }}" method="POST">
                @csrf
                @method('PUT')
                @include('buildings._form')
                <div class="form-actions">
                    <a href="{{ route('buildings.show', $building) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
