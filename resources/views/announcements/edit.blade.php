<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengumuman', 'url' => route('announcements.index')], ['label' => $announcement->title, 'url' => route('announcements.show', $announcement)], ['label' => 'Edit']]">
    <x-page-header title="Edit Pengumuman" :description="$announcement->title" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('announcements.update', $announcement) }}" method="POST">
                @csrf
                @method('PUT')
                @include('announcements._form')
                <div class="form-actions">
                    <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
