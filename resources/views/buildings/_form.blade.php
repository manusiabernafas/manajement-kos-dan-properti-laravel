@php $building = $building ?? null; @endphp

<div class="form-row">
    <x-select label="Properti" name="property_id" required :options="$properties" :value="$building->property_id ?? $selectedPropertyId ?? ''" />
    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$building->status ?? 'active'" />
</div>

<div class="form-row">
    <x-input label="Nama Gedung" name="name" :value="$building->name ?? ''" required />
    <x-input label="Kode Gedung" name="code" :value="$building->code ?? ''" required hint="Contoh: A, B, GD1" />
</div>

<x-input label="Jumlah Lantai" name="number_of_floors" type="number" min="1" :value="$building->number_of_floors ?? 1" required />

<x-textarea label="Deskripsi" name="description" :value="$building->description ?? ''" />
