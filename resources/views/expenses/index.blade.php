<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengeluaran']]">
    <x-page-header title="Pengeluaran" description="Kelola pengeluaran operasional properti">
        <x-slot:actions>
            @can('expenses.create')
                <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Pengeluaran</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. atau deskripsi...">
                </div>
                <select name="property_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Properti</option>
                    @foreach($properties as $id => $name)
                        <option value="{{ $id }}" @selected(request('property_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($expenses->isEmpty())
            <x-empty-state icon="fa-solid fa-receipt" title="Belum ada pengeluaran" description="Catat pengeluaran pertama untuk properti Anda.">
                <x-slot:action>
                    @can('expenses.create')
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">+ Tambah Pengeluaran</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>No.</th><th>Properti</th><th>Kategori</th><th>Tanggal</th><th class="text-right">Jumlah</th><th></th></tr></thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td class="table-cell-primary">{{ $expense->expense_number }}</td>
                                <td>{{ $expense->property->name }}</td>
                                <td>{{ ucfirst($expense->category) }}</td>
                                <td>{{ $expense->date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('expenses.show', $expense) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Detail</a>
                                            @can('expenses.update')
                                                <a href="{{ route('expenses.edit', $expense) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                            @endcan
                                            @can('expenses.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('expenses.destroy', $expense)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $expenses->links() }}
        @endif
    </div>
</x-layouts.app>
