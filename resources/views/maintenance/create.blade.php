<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Maintenance', 'url' => route('maintenance.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Tiket Maintenance" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('maintenance.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <x-select label="Properti" name="property_id" required :options="$properties" />
                    <x-select label="Kamar" name="room_id" :options="$rooms->mapWithKeys(fn($r) => [$r->id => $r->room_number])" />
                </div>
                <div class="form-row">
                    <x-select label="Penghuni Terkait" name="tenant_id" :options="$tenants->mapWithKeys(fn($t) => [$t->id => $t->full_name])" />
                    <x-input label="Kategori" name="category" required hint="Contoh: Listrik, Plumbing, AC" />
                </div>
                <x-input label="Judul" name="title" required />
                <x-textarea label="Deskripsi" name="description" required />
                <div class="form-row">
                    <x-select label="Prioritas" name="priority" required :options="['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent']" value="medium" />
                    <x-select label="Ditugaskan Kepada" name="assigned_to" :options="$technicians->mapWithKeys(fn($u) => [$u->id => $u->name])" />
                </div>
                <x-input label="Estimasi Biaya (Rp)" name="estimated_cost" type="number" min="0" />
                <div class="form-actions">
                    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Tiket</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
