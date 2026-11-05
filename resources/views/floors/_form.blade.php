@php $floor = $floor ?? null; @endphp

<x-select label="Gedung" name="building_id" required :options="$buildings" :value="$floor->building_id ?? $selectedBuildingId ?? ''" />

<div class="form-row">
    <x-input label="Nomor Lantai" name="floor_number" type="number" min="0" :value="$floor->floor_number ?? ''" required />
    <x-input label="Nama Lantai" name="name" :value="$floor->name ?? ''" hint="Opsional, contoh: Lantai Dasar" />
</div>

<x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$floor->status ?? 'active'" />

<x-textarea label="Deskripsi" name="description" :value="$floor->description ?? ''" />
