<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Komplain', 'url' => route('complaints.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Komplain" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('complaints.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <x-select label="Penghuni" name="tenant_id" required :options="$tenants->mapWithKeys(fn($t) => [$t->id => $t->full_name])" />
                    <x-select label="Properti" name="property_id" required :options="$properties" />
                </div>
                <x-input label="Kategori" name="category" required hint="Contoh: Fasilitas, Kebersihan, Keamanan" />
                <x-input label="Judul" name="title" required />
                <x-textarea label="Deskripsi" name="description" required />
                <x-select label="Prioritas" name="priority" required :options="['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent']" value="medium" />
                <div class="form-actions">
                    <a href="{{ route('complaints.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Komplain</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
