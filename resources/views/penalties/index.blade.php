<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Denda']]">
    <x-page-header title="Denda" description="Kelola konfigurasi dan riwayat denda keterlambatan">
        <x-slot:actions>
            @can('penalties.create')
                <button type="button" class="btn btn-primary" data-modal-open="modal-add-config"><i class="fa-solid fa-plus"></i> Tambah Konfigurasi</button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-24">
        <div class="card-header"><h3>Konfigurasi Denda</h3></div>
        @if($configs->isEmpty())
            <div class="card-body"><p class="text-muted text-sm">Belum ada konfigurasi denda. Tambahkan agar sistem otomatis menerapkan denda keterlambatan.</p></div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Properti</th><th>Tipe Perhitungan</th><th class="text-right">Nilai</th><th class="text-center">Grace Period</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($configs as $config)
                            <tr>
                                <td>{{ $config->property?->name ?? 'Semua Properti (Default)' }}</td>
                                <td>{{ ['fixed' => 'Tetap', 'per_day' => 'Per Hari', 'percentage' => 'Persentase'][$config->calculation_type] }}</td>
                                <td class="text-right">{{ $config->calculation_type === 'percentage' ? $config->amount.'%' : 'Rp '.number_format($config->amount, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $config->grace_period_days }} hari</td>
                                <td><x-status-badge :status="$config->status" :colors="['active' => 'success', 'inactive' => 'muted']" :labels="['active' => 'Aktif', 'inactive' => 'Nonaktif']" /></td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-trigger" data-dropdown-toggle><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            @can('penalties.update')
                                                <button type="button" class="dropdown-item" data-modal-open="modal-edit-config-{{ $config->id }}"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
                                            @endcan
                                            @can('penalties.delete')
                                                <div class="dropdown-divider"></div>
                                                <x-delete-form :action="route('penalty-configs.destroy', $config)" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <x-modal id="modal-edit-config-{{ $config->id }}" title="Edit Konfigurasi Denda">
                                <form action="{{ route('penalty-configs.update', $config) }}" method="POST" id="form-edit-config-{{ $config->id }}">
                                    @csrf @method('PUT')
                                    <x-select label="Properti" name="property_id" :options="$properties" :value="$config->property_id" placeholder="Semua Properti (Default)" />
                                    <x-select label="Tipe Perhitungan" name="calculation_type" required :options="['fixed' => 'Tetap', 'per_day' => 'Per Hari', 'percentage' => 'Persentase']" :value="$config->calculation_type" />
                                    <x-input label="Nilai" name="amount" type="number" min="0" step="0.01" :value="$config->amount" required />
                                    <x-input label="Grace Period (hari)" name="grace_period_days" type="number" min="0" :value="$config->grace_period_days" required />
                                    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$config->status" />
                                </form>
                                <x-slot:footer>
                                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                                    <button type="submit" form="form-edit-config-{{ $config->id }}" class="btn btn-primary">Simpan</button>
                                </x-slot:footer>
                            </x-modal>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header"><h3>Riwayat Denda Diterapkan</h3></div>
        @if($penalties->isEmpty())
            <div class="card-body"><p class="text-muted text-sm">Belum ada denda yang diterapkan.</p></div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Invoice</th><th>Penghuni</th><th>Tanggal</th><th class="text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @foreach($penalties as $penalty)
                            <tr>
                                <td><a href="{{ route('invoices.show', $penalty->invoice) }}">{{ $penalty->invoice->invoice_number }}</a></td>
                                <td>{{ $penalty->invoice->tenant->full_name }}</td>
                                <td>{{ $penalty->applied_date->format('d M Y') }}</td>
                                <td class="text-right">Rp {{ number_format($penalty->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $penalties->links() }}
        @endif
    </div>

    <x-modal id="modal-add-config" title="Tambah Konfigurasi Denda">
        <form action="{{ route('penalty-configs.store') }}" method="POST" id="form-add-config">
            @csrf
            <x-select label="Properti" name="property_id" :options="$properties" placeholder="Semua Properti (Default)" />
            <x-select label="Tipe Perhitungan" name="calculation_type" required :options="['fixed' => 'Tetap', 'per_day' => 'Per Hari', 'percentage' => 'Persentase']" />
            <x-input label="Nilai" name="amount" type="number" min="0" step="0.01" required hint="Rp untuk tetap/per hari, % untuk persentase" />
            <x-input label="Grace Period (hari)" name="grace_period_days" type="number" min="0" value="0" required />
            <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" value="active" />
        </form>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
            <button type="submit" form="form-add-config" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-modal>
</x-layouts.app>
