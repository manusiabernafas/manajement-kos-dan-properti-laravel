<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Tipe Kamar', 'url' => route('room-types.index')], ['label' => 'Edit']]">
    <x-page-header title="Edit Tipe Kamar" :description="$roomType->name" />
    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('room-types.update', $roomType) }}" method="POST">
                @csrf
                @method('PUT')
                @include('room-types._form')
                <div class="form-actions">
                    <a href="{{ route('room-types.show', $roomType) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
