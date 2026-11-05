<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Penghuni', 'url' => route('tenants.index')], ['label' => $tenant->full_name, 'url' => route('tenants.show', $tenant)], ['label' => 'Edit']]">
    <x-page-header title="Edit Penghuni" :description="$tenant->full_name" />
    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('tenants._form')
                <div class="form-actions">
                    <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
