<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Kamar', 'url' => route('rooms.index')], ['label' => $room->room_number, 'url' => route('rooms.show', $room)], ['label' => 'Edit']]">
    <x-page-header title="Edit Kamar" :description="$room->room_number" />
    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('rooms.update', $room) }}" method="POST">
                @csrf
                @method('PUT')
                @include('rooms._form')
                <div class="form-actions">
                    <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
