@php $tenant = $tenant ?? null; @endphp

<div class="form-section-title">Data Pribadi</div>
<div class="form-row">
    <x-input label="Nama Lengkap" name="full_name" :value="$tenant->full_name ?? ''" required />
    <x-input label="NIK" name="nik" :value="$tenant->nik ?? ''" />
</div>

<div class="form-row">
    <x-select label="Jenis Kelamin" name="gender" required :options="['male' => 'Laki-laki', 'female' => 'Perempuan']" :value="$tenant->gender ?? ''" />
    <x-select label="Status" name="status" required :options="['active' => 'Aktif', 'inactive' => 'Nonaktif', 'former' => 'Mantan Penghuni']" :value="$tenant->status ?? 'active'" />
</div>

<div class="form-row">
    <x-input label="Telepon" name="phone" :value="$tenant->phone ?? ''" required />
    <x-input label="Email" name="email" type="email" :value="$tenant->email ?? ''" />
</div>

<div class="form-row">
    <x-input label="Tempat Lahir" name="birth_place" :value="$tenant->birth_place ?? ''" />
    <x-input label="Tanggal Lahir" name="birth_date" type="date" :value="$tenant?->birth_date?->format('Y-m-d') ?? ''" />
</div>

<x-textarea label="Alamat" name="address" :value="$tenant->address ?? ''" />

<div class="form-row">
    <x-input label="Pekerjaan" name="occupation" :value="$tenant->occupation ?? ''" />
    <x-input label="Perusahaan/Kampus" name="company" :value="$tenant->company ?? ''" />
</div>

<x-input label="Foto" name="photo" type="file" accept="image/*" />

<div class="form-section-title">Kontak Darurat</div>
<div class="form-row">
    <x-input label="Nama" name="emergency_contact_name" :value="$tenant->emergency_contact_name ?? ''" />
    <x-input label="Telepon" name="emergency_contact_phone" :value="$tenant->emergency_contact_phone ?? ''" />
</div>
<x-input label="Hubungan" name="emergency_contact_relation" :value="$tenant->emergency_contact_relation ?? ''" hint="Contoh: Orang Tua, Saudara" />

<x-textarea label="Catatan" name="notes" :value="$tenant->notes ?? ''" />
