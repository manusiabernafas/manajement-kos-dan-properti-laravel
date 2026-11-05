<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Properti', 'url' => route('properties.index')], ['label' => $property->name, 'url' => route('properties.show', $property)], ['label' => 'Edit']]">
    <x-page-header title="Edit Properti" :description="$property->name" />

    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('properties.update', $property) }}" method="POST">
                @csrf
                @method('PUT')
                @include('properties._form')
                <div class="form-actions">
                    <a href="{{ route('properties.show', $property) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
