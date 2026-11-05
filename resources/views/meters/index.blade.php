<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Meter']]">
    <x-page-header title="Meter" description="Kelola meter listrik dan air per kamar">
        <x-slot:actions>
            @can('meters.create')
                <button type="button" class="btn btn-primary" data-modal-open="modal-add-meter"><i class="fa-solid fa-plus"></i> Tambah Meter</button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <select name="property_id" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Properti</option>
                    @foreach($properties as $id => $name)
                        <option value="{{ $id }}" @selected(request('property_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="meter_type" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="electricity" @selected(request('meter_type') === 'electricity')>Listrik</option>
                    <option value="water" @selected(request('meter_type') === 'water')>Air</option>
                </select>
            </div>
        </form>

        @if($meters->isEmpty())
            <x-empty-state icon="fa-solid fa-gauge-high" title="Belum ada meter" description="Tambahkan meter listrik/air untuk kamar.">
                <x-slot:action>
                    @can('meters.create')
                        <button type="button" class="btn btn-primary" data-modal-open="modal-add-meter">+ Tambah Meter</button>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Kamar</th><th>Tipe</th><th>No. Meter</th><th class="text-right">Meter Terakhir</th><th class="text-right">Tarif</th><th></th></tr></thead>
                    <tbody>
                        @foreach($meters as $meter)
                            <tr>
                                <td class="table-cell-primary">{{ $meter->room->room_number }}</td>
                                <td>{{ $meter->meter_type === 'electricity' ? 'Listrik' : 'Air' }}</td>
                                <td>{{ $meter->meter_number ?? '-' }}</td>
                                <td class="text-right">{{ $meter->latestReading()?->current_value ?? 0 }} {{ $meter->unit }}</td>
                                <td class="text-right">Rp {{ number_format($meter->tariff, 0, ',', '.') }}</td>
                                <td class="text-right"><a href="{{ route('meters.show', $meter) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $meters->links() }}
        @endif
    </div>

    <x-modal id="modal-add-meter" title="Tambah Meter">
        <form action="{{ route('meters.store') }}" method="POST" id="form-add-meter">
            @csrf
            <x-select label="Kamar" name="room_id" required :options="$rooms->mapWithKeys(fn($r) => [$r->id => $r->room_number])" />
            <x-select label="Tipe Meter" name="meter_type" required :options="['electricity' => 'Listrik', 'water' => 'Air']" />
            <x-input label="Nomor Meter" name="meter_number" />
            <div class="form-row">
                <x-input label="Satuan" name="unit" value="kWh" required />
                <x-input label="Tarif per Satuan (Rp)" name="tariff" type="number" min="0" required />
            </div>
        </form>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
            <button type="submit" form="form-add-meter" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-modal>
</x-layouts.app>
