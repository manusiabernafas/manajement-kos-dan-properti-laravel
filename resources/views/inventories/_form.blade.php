@php $inventory = $inventory ?? null; @endphp

<div class="form-row">
    <x-select label="Properti" name="property_id" required :options="$properties" :value="$inventory->property_id ?? ''" />
    <x-select label="Kamar" name="room_id" :options="$rooms->mapWithKeys(fn($r) => [$r->id => $r->room_number])" :value="$inventory->room_id ?? ''" />
</div>

<div class="form-row">
    <x-input label="Kategori" name="category" required hint="Contoh: Elektronik, Furnitur" :value="$inventory->category ?? ''" />
    <x-input label="Nama Barang" name="name" required :value="$inventory->name ?? ''" />
</div>

<div class="form-row">
    <x-input label="Nomor Seri" name="serial_number" :value="$inventory->serial_number ?? ''" />
    <x-input label="Tanggal Beli" name="purchase_date" type="date" :value="$inventory?->purchase_date?->format('Y-m-d') ?? ''" />
</div>

<x-input label="Harga Beli (Rp)" name="purchase_price" type="number" min="0" :value="$inventory->purchase_price ?? ''" />

<div class="form-row">
    <x-select label="Kondisi" name="condition" required :options="['good' => 'Baik', 'minor_damage' => 'Rusak Ringan', 'major_damage' => 'Rusak Berat', 'lost' => 'Hilang']" :value="$inventory->condition ?? 'good'" />
    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'maintenance' => 'Maintenance', 'lost' => 'Hilang', 'retired' => 'Retired']" :value="$inventory->status ?? 'active'" />
</div>

<x-textarea label="Catatan" name="notes" :value="$inventory->notes ?? ''" />
