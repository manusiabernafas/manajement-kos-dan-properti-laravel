<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengumuman', 'url' => route('announcements.index')], ['label' => 'Buat Pengumuman']]">
    <x-page-header title="Buat Pengumuman" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('announcements.store') }}" method="POST">
                @csrf
                @include('announcements._form')
                <div class="form-actions">
                    <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Pengumuman</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
