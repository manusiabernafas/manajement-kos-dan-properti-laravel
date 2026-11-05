@php $roomType = $roomType ?? null; @endphp

<div class="form-row">
    <x-select label="Properti" name="property_id" required :options="$properties" :value="$roomType->property_id ?? ''" />
    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$roomType->status ?? 'active'" />
</div>

<div class="form-row">
    <x-input label="Nama Tipe" name="name" :value="$roomType->name ?? ''" required />
    <x-input label="Kode" name="code" :value="$roomType->code ?? ''" required />
</div>

<div class="form-row">
    <x-input label="Kapasitas (orang)" name="capacity" type="number" min="1" :value="$roomType->capacity ?? 1" required />
    <x-input label="Ukuran" name="size" :value="$roomType->size ?? ''" hint="Contoh: 3x4 m" />
</div>

<div class="form-section-title">Harga</div>
<div class="form-row">
    <x-input label="Harga Bulanan (Rp)" name="monthly_price" type="number" min="0" :value="$roomType->monthly_price ?? ''" required />
    <x-input label="Deposit (Rp)" name="deposit_amount" type="number" min="0" :value="$roomType->deposit_amount ?? 0" required />
</div>
<div class="form-row">
    <x-input label="Harga Mingguan (Rp)" name="weekly_price" type="number" min="0" :value="$roomType->weekly_price ?? ''" />
    <x-input label="Harga Harian (Rp)" name="daily_price" type="number" min="0" :value="$roomType->daily_price ?? ''" />
</div>

<x-textarea label="Deskripsi" name="description" :value="$roomType->description ?? ''" />

<div class="form-section-title">Fasilitas</div>
<div class="grid grid-cols-3">
    @foreach($facilities as $facility)
        <x-checkbox :label="$facility->name" name="facilities[]" :value="$facility->id" :checked="in_array($facility->id, $selectedFacilities ?? [])" />
    @endforeach
</div>
