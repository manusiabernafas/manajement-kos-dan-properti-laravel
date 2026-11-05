<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Kamar', 'url' => route('rooms.index')], ['label' => $room->room_number]]">
    <x-page-header :title="'Kamar '.$room->room_number" :description="$room->property->name.' — '.$room->building->name.' — Lantai '.$room->floor->floor_number">
        <x-slot:actions>
            <x-badge :variant="\App\Models\Room::STATUS_COLORS[$room->status]">{{ ucfirst($room->status) }}</x-badge>
            @can('rooms.update')
                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
            @if(($room->status === 'available' || $room->status === 'reserved') && \Illuminate\Support\Facades\Route::has('check-ins.create'))
                @can('check-ins.create')
                    <a href="{{ route('check-ins.create', ['room_id' => $room->id]) }}" class="btn btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Check-in</a>
                @endcan
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-4 mb-24">
        <x-stat-card label="Harga/Bulan" :value="'Rp '.number_format($room->price, 0, ',', '.')" icon="fa-solid fa-tag" variant="primary" />
        <x-stat-card label="Deposit" :value="'Rp '.number_format($room->deposit, 0, ',', '.')" icon="fa-solid fa-piggy-bank" variant="info" />
        <x-stat-card label="Kapasitas" :value="$room->capacity.' orang'" icon="fa-solid fa-users" variant="success" />
        <x-stat-card label="Tipe Kamar" :value="$room->roomType->name" icon="fa-solid fa-bed" variant="warning" />
    </div>

    <div class="tabs" id="room-tabs">
        <div class="tab-link active" data-tab="overview">Overview</div>
        <div class="tab-link" data-tab="contract">Kontrak</div>
        <div class="tab-link" data-tab="invoices">Tagihan</div>
        <div class="tab-link" data-tab="maintenance">Maintenance</div>
        <div class="tab-link" data-tab="complaints">Komplain</div>
        <div class="tab-link" data-tab="inventory">Inventaris</div>
        <div class="tab-link" data-tab="history">Riwayat Penghuni</div>
    </div>

    <div data-tab-panel="overview">
        <div class="grid grid-cols-3">
            <div class="card">
                <div class="card-header"><h3>Penghuni Saat Ini</h3></div>
                <div class="card-body">
                    @if($room->activeContract?->tenant)
                        <p class="font-semibold">{{ $room->activeContract->tenant->full_name }}</p>
                        <p class="text-muted text-sm mt-8">{{ $room->activeContract->tenant->phone }}</p>
                        <a href="{{ route('tenants.show', $room->activeContract->tenant) }}" class="btn btn-outline btn-sm mt-16">Lihat Profil</a>
                    @else
                        <p class="text-muted text-sm">Kamar belum ada penghuni.</p>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Fasilitas</h3></div>
                <div class="card-body">
                    @forelse($room->facilities as $facility)
                        <span class="badge badge-muted mb-8" style="margin-right:6px;">{{ $facility->name }}</span>
                    @empty
                        <p class="text-muted text-sm">Belum ada fasilitas.</p>
                    @endforelse
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Meter</h3></div>
                <div class="card-body">
                    @forelse($room->meters as $meter)
                        <div class="flex justify-between mb-8">
                            <span class="text-sm">{{ ucfirst($meter->meter_type) }}</span>
                            <span class="text-sm font-semibold">{{ $meter->latestReading()?->current_value ?? '-' }} {{ $meter->unit }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-sm">Belum ada meter terpasang.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div data-tab-panel="contract" style="display:none;">
        <div class="card">
            <div class="card-body">
                @if($room->activeContract)
                    <div class="grid grid-cols-3">
                        <div><div class="text-muted text-sm">No. Kontrak</div><div class="font-semibold">{{ $room->activeContract->contract_number }}</div></div>
                        <div><div class="text-muted text-sm">Mulai</div><div class="font-semibold">{{ $room->activeContract->start_date->format('d M Y') }}</div></div>
                        <div><div class="text-muted text-sm">Berakhir</div><div class="font-semibold">{{ $room->activeContract->end_date->format('d M Y') }}</div></div>
                    </div>
                    <a href="{{ route('contracts.show', $room->activeContract) }}" class="btn btn-outline btn-sm mt-16">Lihat Kontrak Lengkap</a>
                @else
                    <x-empty-state icon="fa-solid fa-file-signature" title="Tidak ada kontrak aktif" />
                @endif
            </div>
        </div>
    </div>

    <div data-tab-panel="invoices" style="display:none;">
        <div class="card">
            @if($room->invoices->isEmpty())
                <x-empty-state icon="fa-solid fa-file-invoice" title="Belum ada tagihan" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>No. Invoice</th><th>Periode</th><th class="text-right">Total</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($room->invoices as $invoice)
                                <tr>
                                    <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                                    <td>{{ $invoice->issue_date->format('M Y') }}</td>
                                    <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                    <td><x-status-badge :status="$invoice->status" :colors="\App\Models\Invoice::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div data-tab-panel="maintenance" style="display:none;">
        <div class="card">
            @if($room->maintenanceTickets->isEmpty())
                <x-empty-state icon="fa-solid fa-screwdriver-wrench" title="Belum ada maintenance" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>Tiket</th><th>Judul</th><th>Prioritas</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($room->maintenanceTickets as $ticket)
                                <tr>
                                    <td><a href="{{ route('maintenance.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                                    <td>{{ $ticket->title }}</td>
                                    <td><x-status-badge :status="$ticket->priority" :colors="\App\Models\MaintenanceTicket::PRIORITY_COLORS" /></td>
                                    <td><x-status-badge :status="$ticket->status" :colors="\App\Models\MaintenanceTicket::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div data-tab-panel="complaints" style="display:none;">
        <div class="card">
            @if($room->complaints->isEmpty())
                <x-empty-state icon="fa-solid fa-comment-dots" title="Belum ada komplain" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>Tiket</th><th>Judul</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($room->complaints as $complaint)
                                <tr>
                                    <td><a href="{{ route('complaints.show', $complaint) }}">{{ $complaint->ticket_number }}</a></td>
                                    <td>{{ $complaint->title }}</td>
                                    <td><x-status-badge :status="$complaint->status" :colors="\App\Models\Complaint::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div data-tab-panel="inventory" style="display:none;">
        <div class="card">
            @if($room->inventories->isEmpty())
                <x-empty-state icon="fa-solid fa-boxes-stacked" title="Belum ada inventaris" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>Kode</th><th>Nama</th><th>Kondisi</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($room->inventories as $item)
                                <tr>
                                    <td>{{ $item->inventory_code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td><x-status-badge :status="$item->condition" :colors="\App\Models\Inventory::CONDITION_COLORS" /></td>
                                    <td><x-status-badge :status="$item->status" :colors="\App\Models\Inventory::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div data-tab-panel="history" style="display:none;">
        <div class="card">
            @if($room->contracts->isEmpty())
                <x-empty-state icon="fa-solid fa-clock-rotate-left" title="Belum ada riwayat penghuni" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>Penghuni</th><th>Mulai</th><th>Berakhir</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($room->contracts as $contract)
                                <tr>
                                    <td><a href="{{ route('tenants.show', $contract->tenant) }}">{{ $contract->tenant->full_name }}</a></td>
                                    <td>{{ $contract->start_date->format('d M Y') }}</td>
                                    <td>{{ $contract->end_date->format('d M Y') }}</td>
                                    <td><x-status-badge :status="$contract->status" :colors="\App\Models\Contract::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('#room-tabs .tab-link').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('#room-tabs .tab-link').forEach(function(t) { t.classList.remove('active'); });
                document.querySelectorAll('[data-tab-panel]').forEach(function(p) { p.style.display = 'none'; });
                tab.classList.add('active');
                document.querySelector('[data-tab-panel="' + tab.dataset.tab + '"]').style.display = 'block';
            });
        });
    </script>
    @endpush
</x-layouts.app>
