<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Kontrak']]">
    <x-page-header title="Kontrak" description="Kelola seluruh kontrak sewa" />

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. kontrak...">
                </div>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                    <option value="terminated" @selected(request('status') === 'terminated')>Terminated</option>
                </select>
            </div>
        </form>

        @if($contracts->isEmpty())
            <x-empty-state icon="fa-solid fa-file-signature" title="Belum ada kontrak" description="Kontrak akan otomatis terbuat saat proses check-in." />
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>No. Kontrak</th><th>Penghuni</th><th>Kamar</th><th>Mulai</th><th>Berakhir</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($contracts as $contract)
                            <tr>
                                <td class="table-cell-primary">{{ $contract->contract_number }}</td>
                                <td>{{ $contract->tenant->full_name }}</td>
                                <td>{{ $contract->room->room_number }}</td>
                                <td>{{ $contract->start_date->format('d M Y') }}</td>
                                <td>
                                    {{ $contract->end_date->format('d M Y') }}
                                    @if($contract->status === 'active' && $contract->daysUntilExpiry() <= 30 && $contract->daysUntilExpiry() >= 0)
                                        <div class="text-xs" style="color:var(--color-warning);">{{ $contract->daysUntilExpiry() }} hari lagi</div>
                                    @endif
                                </td>
                                <td><x-status-badge :status="$contract->status" :colors="\App\Models\Contract::STATUS_COLORS" /></td>
                                <td class="text-right"><a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $contracts->links() }}
        @endif
    </div>
</x-layouts.app>
