<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Penghuni', 'url' => route('tenants.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Penghuni" />
    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ route('tenants.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('tenants._form')
                <div class="form-actions">
                    <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Penghuni</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
