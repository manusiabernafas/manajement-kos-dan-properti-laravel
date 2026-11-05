<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Tagihan']]">
    <x-page-header title="Tagihan" description="Kelola tagihan sewa dan biaya lainnya">
        <x-slot:actions>
            @can('invoices.create')
                <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Tagihan</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. invoice...">
                </div>
                <select name="property_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Properti</option>
                    @foreach($properties as $id => $name)
                        <option value="{{ $id }}" @selected(request('property_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="unpaid" @selected(request('status') === 'unpaid')>Unpaid</option>
                    <option value="partial" @selected(request('status') === 'partial')>Partial</option>
                    <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                    <option value="overdue" @selected(request('status') === 'overdue')>Overdue</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                </select>
            </div>
        </form>

        @if($invoices->isEmpty())
            <x-empty-state icon="fa-solid fa-file-invoice-dollar" title="Belum ada tagihan" description="Tagihan akan otomatis dibuat saat check-in atau bisa dibuat manual.">
                <x-slot:action>
                    @can('invoices.create')
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">+ Buat Tagihan</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr><th>Invoice</th><th>Penghuni</th><th>Kamar</th><th>Jatuh Tempo</th><th class="text-right">Total</th><th class="text-right">Sisa</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td class="table-cell-primary">{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->tenant->full_name }}</td>
                                <td>{{ $invoice->room->room_number }}</td>
                                <td>{{ $invoice->due_date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$invoice->status" :colors="\App\Models\Invoice::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $invoices->links() }}
        @endif
    </div>
</x-layouts.app>
