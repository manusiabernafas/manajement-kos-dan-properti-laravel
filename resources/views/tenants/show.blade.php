<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Penghuni', 'url' => route('tenants.index')], ['label' => $tenant->full_name]]">
    <div class="card mb-24">
        <div class="profile-header">
            <img src="{{ $tenant->photo ? asset('storage/'.$tenant->photo) : 'https://ui-avatars.com/api/?name='.urlencode($tenant->full_name).'&background=5965F2&color=fff' }}" alt="">
            <div class="profile-header-info">
                <h2>{{ $tenant->full_name }}</h2>
                <div class="profile-header-meta">
                    <span><i class="fa-solid fa-id-card"></i> {{ $tenant->tenant_code }}</span>
                    <span><i class="fa-solid fa-phone"></i> {{ $tenant->phone }}</span>
                    @if($tenant->email)<span><i class="fa-solid fa-envelope"></i> {{ $tenant->email }}</span>@endif
                    @if($tenant->activeContract?->room)<span><i class="fa-solid fa-door-open"></i> {{ $tenant->activeContract->room->room_number }}</span>@endif
                    <x-status-badge :status="$tenant->status" :colors="['active' => 'success', 'inactive' => 'muted', 'former' => 'danger']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif', 'former' => 'Mantan']" />
                </div>
            </div>
            <div class="profile-header-actions">
                @can('tenants.update')
                    <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                @endcan
            </div>
        </div>
    </div>

    <div class="tabs" id="tenant-tabs">
        <div class="tab-link active" data-tab="overview">Overview</div>
        <div class="tab-link" data-tab="contract">Kontrak</div>
        <div class="tab-link" data-tab="invoices">Tagihan</div>
        <div class="tab-link" data-tab="payments">Pembayaran</div>
        <div class="tab-link" data-tab="complaints">Komplain</div>
        <div class="tab-link" data-tab="maintenance">Maintenance</div>
        <div class="tab-link" data-tab="documents">Dokumen</div>
        <div class="tab-link" data-tab="activity">Aktivitas</div>
    </div>

    <div data-tab-panel="overview">
        <div class="grid grid-cols-3">
            <div class="card">
                <div class="card-header"><h3>Data Pribadi</h3></div>
                <div class="card-body">
                    <div class="flex justify-between mb-12"><span class="text-muted text-sm">Jenis Kelamin</span><span>{{ $tenant->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                    <div class="flex justify-between mb-12"><span class="text-muted text-sm">NIK</span><span>{{ $tenant->nik ?? '-' }}</span></div>
                    <div class="flex justify-between mb-12"><span class="text-muted text-sm">Tempat, Tgl Lahir</span><span>{{ $tenant->birth_place }}{{ $tenant->birth_date ? ', '.$tenant->birth_date->format('d M Y') : '' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted text-sm">Pekerjaan</span><span>{{ $tenant->occupation ?? '-' }}</span></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Alamat</h3></div>
                <div class="card-body"><p class="text-sm">{{ $tenant->address ?? '-' }}</p></div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Kontak Darurat</h3></div>
                <div class="card-body">
                    <p class="text-sm font-semibold">{{ $tenant->emergency_contact_name ?? '-' }}</p>
                    <p class="text-sm text-muted mt-8">{{ $tenant->emergency_contact_phone ?? '-' }} ({{ $tenant->emergency_contact_relation ?? '-' }})</p>
                </div>
            </div>
        </div>
    </div>

    <div data-tab-panel="contract" style="display:none;">
        <div class="card">
            @if($tenant->contracts->isEmpty())
                <x-empty-state icon="fa-solid fa-file-signature" title="Belum ada kontrak" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>No. Kontrak</th><th>Kamar</th><th>Mulai</th><th>Berakhir</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($tenant->contracts as $contract)
                                <tr>
                                    <td><a href="{{ route('contracts.show', $contract) }}">{{ $contract->contract_number }}</a></td>
                                    <td>{{ $contract->room->room_number }}</td>
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

    <div data-tab-panel="invoices" style="display:none;">
        <div class="card">
            @if($tenant->invoices->isEmpty())
                <x-empty-state icon="fa-solid fa-file-invoice" title="Belum ada tagihan" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>No. Invoice</th><th>Periode</th><th class="text-right">Total</th><th class="text-right">Sisa</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($tenant->invoices as $invoice)
                                <tr>
                                    <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                                    <td>{{ $invoice->issue_date->format('M Y') }}</td>
                                    <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</td>
                                    <td><x-status-badge :status="$invoice->status" :colors="\App\Models\Invoice::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div data-tab-panel="payments" style="display:none;">
        <div class="card">
            @if($tenant->payments->isEmpty())
                <x-empty-state icon="fa-solid fa-money-check-dollar" title="Belum ada pembayaran" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>No. Pembayaran</th><th>Tanggal</th><th class="text-right">Jumlah</th><th>Metode</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($tenant->payments as $payment)
                                <tr>
                                    <td><a href="{{ route('payments.show', $payment) }}">{{ $payment->payment_number }}</a></td>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td>{{ strtoupper(str_replace('_',' ',$payment->payment_method)) }}</td>
                                    <td><x-status-badge :status="$payment->status" :colors="\App\Models\Payment::STATUS_COLORS" /></td>
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
            @if($tenant->complaints->isEmpty())
                <x-empty-state icon="fa-solid fa-comment-dots" title="Belum ada komplain" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>Tiket</th><th>Judul</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($tenant->complaints as $complaint)
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

    <div data-tab-panel="maintenance" style="display:none;">
        <div class="card">
            @if($tenant->maintenanceTickets->isEmpty())
                <x-empty-state icon="fa-solid fa-screwdriver-wrench" title="Belum ada maintenance" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>Tiket</th><th>Judul</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($tenant->maintenanceTickets as $ticket)
                                <tr>
                                    <td><a href="{{ route('maintenance.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                                    <td>{{ $ticket->title }}</td>
                                    <td><x-status-badge :status="$ticket->status" :colors="\App\Models\MaintenanceTicket::STATUS_COLORS" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div data-tab-panel="documents" style="display:none;">
        <div class="card">
            <div class="card-header">
                <h3>Dokumen</h3>
                @can('tenant-documents.create')
                    <button type="button" class="btn btn-sm btn-primary" data-modal-open="modal-upload-doc"><i class="fa-solid fa-upload"></i> Unggah</button>
                @endcan
            </div>
            <div class="card-body">
                @if($tenant->documents->isEmpty())
                    <x-empty-state icon="fa-solid fa-folder-open" title="Belum ada dokumen" />
                @else
                    @foreach($tenant->documents as $doc)
                        <div class="file-preview">
                            <i class="fa-solid fa-file"></i>
                            <div>
                                <div class="font-medium">{{ $doc->original_name }}</div>
                                <div class="text-muted text-xs">{{ strtoupper($doc->document_type) }} — {{ $doc->humanSize() }}</div>
                            </div>
                            <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-ghost btn-sm">Lihat</a>
                            @can('tenant-documents.delete')
                                <form action="{{ route('tenants.documents.destroy', [$tenant, $doc]) }}" method="POST" data-confirm-submit>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="remove-file"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            @endcan
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div data-tab-panel="activity" style="display:none;">
        <div class="card">
            @if($activities->isEmpty())
                <x-empty-state icon="fa-solid fa-clock-rotate-left" title="Belum ada aktivitas tercatat" />
            @else
                <div class="widget-list">
                    @foreach($activities as $activity)
                        <div class="widget-item">
                            <div class="widget-item-icon badge-primary" style="background:var(--color-primary-soft);color:var(--color-primary);"><i class="fa-solid fa-circle-info"></i></div>
                            <div class="widget-item-body">
                                <div class="widget-item-title">{{ ucfirst($activity->action) }} — {{ $activity->module }}</div>
                                <div class="widget-item-desc">{{ $activity->user?->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <x-modal id="modal-upload-doc" title="Unggah Dokumen">
        <form action="{{ route('tenants.documents.store', $tenant) }}" method="POST" enctype="multipart/form-data" id="form-upload-doc">
            @csrf
            <x-select label="Jenis Dokumen" name="document_type" required :options="['ktp' => 'KTP', 'kk' => 'Kartu Keluarga', 'agreement' => 'Perjanjian', 'other' => 'Lainnya']" />
            <x-input label="File" name="file" type="file" required hint="PDF/JPG/PNG, maksimal 5MB" />
        </form>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
            <button type="submit" form="form-upload-doc" class="btn btn-primary">Unggah</button>
        </x-slot:footer>
    </x-modal>

    @push('scripts')
    <script>
        document.querySelectorAll('#tenant-tabs .tab-link').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('#tenant-tabs .tab-link').forEach(function(t) { t.classList.remove('active'); });
                document.querySelectorAll('[data-tab-panel]').forEach(function(p) { p.style.display = 'none'; });
                tab.classList.add('active');
                document.querySelector('[data-tab-panel="' + tab.dataset.tab + '"]').style.display = 'block';
            });
        });
    </script>
    @endpush
</x-layouts.app>
