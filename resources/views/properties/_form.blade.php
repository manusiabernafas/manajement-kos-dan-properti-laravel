@php $property = $property ?? null; @endphp

<div class="form-section-title">Informasi Dasar</div>
<div class="form-row">
    <x-input label="Nama Properti" name="name" :value="$property->name ?? ''" required />
    <x-input label="Kode Properti" name="code" :value="$property->code ?? ''" required hint="Kode unik, contoh: KM01" />
</div>

<x-textarea label="Alamat" name="address" :value="$property->address ?? ''" required />

<div class="form-row">
    <x-input label="Kota" name="city" :value="$property->city ?? ''" />
    <x-input label="Provinsi" name="province" :value="$property->province ?? ''" />
</div>

<div class="form-row">
    <x-input label="Kode Pos" name="postal_code" :value="$property->postal_code ?? ''" />
    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$property->status ?? 'active'" />
</div>

<div class="form-row">
    <x-input label="Telepon" name="phone" :value="$property->phone ?? ''" />
    <x-input label="Email" name="email" type="email" :value="$property->email ?? ''" />
</div>

<x-textarea label="Deskripsi" name="description" :value="$property->description ?? ''" />

<div class="form-section-title">Lokasi Peta</div>
<div class="form-row">
    <x-input label="Latitude" name="latitude" type="number" step="any" :value="$property->latitude ?? ''" />
    <x-input label="Longitude" name="longitude" type="number" :value="$property->longitude ?? ''" />
</div>
<x-input label="Google Maps URL" name="google_maps_url" :value="$property->google_maps_url ?? ''" />

<div class="form-section-title">Rekening Pembayaran</div>
<div class="form-row">
    <x-input label="Nama Bank" name="bank_name" :value="$property->bank_name ?? ''" />
    <x-input label="Nomor Rekening" name="bank_account_number" :value="$property->bank_account_number ?? ''" />
</div>
<x-input label="Atas Nama" name="bank_account_name" :value="$property->bank_account_name ?? ''" />

<div class="form-section-title">Aturan Kost</div>
<x-textarea label="Aturan" name="rules" :value="$property->rules ?? ''" rows="5" />
