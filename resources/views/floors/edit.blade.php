<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Lantai', 'url' => route('floors.index')], ['label' => 'Edit']]">
    <x-page-header title="Edit Lantai" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('floors.update', $floor) }}" method="POST">
                @csrf
                @method('PUT')
                @include('floors._form')
                <div class="form-actions">
                    <a href="{{ route('floors.show', $floor) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
