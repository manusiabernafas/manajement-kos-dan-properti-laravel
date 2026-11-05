<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengeluaran', 'url' => route('expenses.index')], ['label' => $expense->expense_number, 'url' => route('expenses.show', $expense)], ['label' => 'Edit']]">
    <x-page-header title="Edit Pengeluaran" :description="$expense->expense_number" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('expenses._form')
                <div class="form-actions">
                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
