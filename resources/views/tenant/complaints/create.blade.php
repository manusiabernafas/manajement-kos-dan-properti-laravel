<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Buat Komplain" />

    @if(!$tenant->activeContract)
        <x-empty-state icon="fa-solid fa-triangle-exclamation" title="Anda belum memiliki kamar aktif" />
    @else
        <div class="card" style="max-width: 700px;">
            <div class="card-body">
                <form action="{{ route('tenant.complaints.store') }}" method="POST">
                    @csrf
                    <x-input label="Kategori" name="category" required hint="Contoh: Fasilitas, Kebersihan, Keamanan" />
                    <x-input label="Judul" name="title" required />
                    <x-textarea label="Deskripsi" name="description" required />
                    <x-select label="Prioritas" name="priority" required :options="['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent']" value="medium" />
                    <div class="form-actions">
                        <a href="{{ route('tenant.complaints.index') }}" class="btn btn-secondary">Batal</a>
                        <x-button type="submit" variant="primary">Kirim Komplain</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-layouts.app>
