<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengeluaran', 'url' => route('expenses.index')], ['label' => $expense->expense_number]]">
    <x-page-header :title="$expense->expense_number" :description="$expense->property->name">
        <x-slot:actions>
            @can('expenses.update')
                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-2">
        <div class="card">
            <div class="card-header"><h3>Detail Pengeluaran</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Kategori</span><span>{{ ucfirst($expense->category) }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Tanggal</span><span>{{ $expense->date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Jumlah</span><span class="font-semibold">Rp {{ number_format($expense->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Vendor</span><span>{{ $expense->vendor ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Metode</span><span>{{ $expense->payment_method ? strtoupper(str_replace('_',' ',$expense->payment_method)) : '-' }}</span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Deskripsi & Lampiran</h3></div>
            <div class="card-body">
                <p class="text-sm mb-16">{{ $expense->description }}</p>
                @if($expense->attachment_path)
                    <a href="{{ asset('storage/'.$expense->attachment_path) }}" target="_blank" class="btn btn-outline btn-sm"><i class="fa-solid fa-paperclip"></i> Lihat Lampiran</a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
