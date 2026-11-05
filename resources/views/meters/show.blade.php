<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Meter', 'url' => route('meters.index')], ['label' => $title]]">
    <x-page-header :title="$title" :description="($meter->meter_type === 'electricity' ? 'Listrik' : 'Air').' — '.$meter->room->property->name">
        <x-slot:actions>
            @can('meters.create')
                <button type="button" class="btn btn-primary" data-modal-open="modal-add-reading"><i class="fa-solid fa-plus"></i> Catat Pembacaan</button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header"><h3>Riwayat Pembacaan</h3></div>
        @if($meter->readings->isEmpty())
            <div class="card-body"><x-empty-state icon="fa-solid fa-gauge-high" title="Belum ada pembacaan" description="Catat pembacaan meter pertama." /></div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Tanggal</th><th class="text-right">Awal</th><th class="text-right">Akhir</th><th class="text-right">Pemakaian</th><th class="text-right">Total</th><th>Invoice</th></tr></thead>
                    <tbody>
                        @foreach($meter->readings as $reading)
                            <tr>
                                <td>{{ $reading->reading_date->format('d M Y') }}</td>
                                <td class="text-right">{{ $reading->previous_value }}</td>
                                <td class="text-right">{{ $reading->current_value }}</td>
                                <td class="text-right">{{ $reading->usage }} {{ $meter->unit }}</td>
                                <td class="text-right">Rp {{ number_format($reading->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($reading->invoice_id)
                                        <a href="{{ route('invoices.show', $reading->invoice_id) }}">Lihat</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <x-modal id="modal-add-reading" title="Catat Pembacaan Meter">
        <form action="{{ route('meters.readings.store', $meter) }}" method="POST" enctype="multipart/form-data" id="form-add-reading">
            @csrf
            <x-input label="Tanggal Pembacaan" name="reading_date" type="date" :value="now()->format('Y-m-d')" required />
            <x-input label="Meter Awal (Referensi)" name="previous_ref" type="text" :value="$meter->latestReading()?->current_value ?? 0" disabled />
            <x-input label="Meter Akhir" name="current_value" type="number" min="0" step="0.01" required />
            <x-input label="Tarif per Satuan (Rp)" name="tariff" type="number" min="0" :value="$meter->tariff" required />
            <x-input label="Foto Meter" name="photo" type="file" accept="image/*" />
            <x-textarea label="Catatan" name="notes" />
            <x-checkbox label="Buat tagihan otomatis dari pembacaan ini" name="generate_invoice" value="1" />
        </form>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
            <button type="submit" form="form-add-reading" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-modal>
</x-layouts.app>
