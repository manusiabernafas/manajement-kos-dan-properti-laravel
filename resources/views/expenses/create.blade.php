<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengeluaran', 'url' => route('expenses.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Pengeluaran" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('expenses._form')
                <div class="form-actions">
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Pengeluaran</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
