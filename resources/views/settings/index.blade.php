<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengaturan']]">
    <x-page-header title="Pengaturan" description="Konfigurasi umum aplikasi KostManager" />

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-24">
            <div class="card-header"><h3>Umum</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <x-input label="Nama Aplikasi" name="app_name" :value="$values['app_name']" required />
                    <x-input label="Nama Perusahaan" name="company_name" :value="$values['company_name']" />
                </div>
                <x-textarea label="Alamat Perusahaan" name="company_address" :value="$values['company_address']" />
                <div class="form-row">
                    <x-input label="Telepon" name="company_phone" :value="$values['company_phone']" />
                    <x-input label="Email" name="company_email" type="email" :value="$values['company_email']" />
                </div>
            </div>
        </div>

        <div class="card mb-24">
            <div class="card-header"><h3>Lokalisasi</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <x-select label="Mata Uang" name="currency" required :options="['IDR' => 'IDR - Rupiah']" :value="$values['currency']" />
                    <x-select label="Zona Waktu" name="timezone" required :options="['Asia/Jakarta' => 'WIB (Asia/Jakarta)', 'Asia/Makassar' => 'WITA (Asia/Makassar)', 'Asia/Jayapura' => 'WIT (Asia/Jayapura)']" :value="$values['timezone']" />
                </div>
                <x-select label="Format Tanggal" name="date_format" required :options="['d/m/Y' => 'DD/MM/YYYY', 'd-m-Y' => 'DD-MM-YYYY', 'Y-m-d' => 'YYYY-MM-DD']" :value="$values['date_format']" />
            </div>
        </div>

        <div class="card mb-24">
            <div class="card-header"><h3>Prefix Nomor Dokumen</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <x-input label="Prefix Invoice" name="invoice_prefix" :value="$values['invoice_prefix']" required />
                    <x-input label="Prefix Pembayaran" name="payment_prefix" :value="$values['payment_prefix']" required />
                </div>
                <div class="form-row">
                    <x-input label="Prefix Penghuni" name="tenant_prefix" :value="$values['tenant_prefix']" required />
                    <x-input label="Prefix Booking" name="booking_prefix" :value="$values['booking_prefix']" required />
                </div>
                <x-input label="Prefix Kontrak" name="contract_prefix" :value="$values['contract_prefix']" required />
            </div>
        </div>

        <div class="card mb-24">
            <div class="card-header"><h3>Billing & Tampilan</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <x-input label="Tanggal Billing Default" name="default_billing_day" type="number" min="1" max="31" :value="$values['default_billing_day']" required />
                    <x-select label="Tema Default" name="default_theme" required :options="['light' => 'Light', 'dark' => 'Dark', 'system' => 'System']" :value="$values['default_theme']" />
                </div>
                <p class="text-muted text-sm">Konfigurasi denda keterlambatan dapat diatur di halaman <a href="{{ route('penalties.index') }}" style="color:var(--color-primary);">Denda</a>.</p>
            </div>
        </div>

        <div class="form-actions">
            <x-button type="submit" variant="primary">Simpan Pengaturan</x-button>
        </div>
    </form>
</x-layouts.app>
