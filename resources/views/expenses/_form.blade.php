@php $expense = $expense ?? null;
$categories = ['electricity' => 'Listrik', 'water' => 'Air', 'internet' => 'Internet', 'salary' => 'Gaji', 'maintenance' => 'Maintenance', 'cleaning' => 'Kebersihan', 'security' => 'Keamanan', 'tax' => 'Pajak', 'furniture' => 'Furnitur', 'marketing' => 'Marketing', 'operational' => 'Operasional', 'other' => 'Lainnya'];
@endphp

<div class="form-row">
    <x-select label="Properti" name="property_id" required :options="$properties" :value="$expense->property_id ?? ''" />
    <x-select label="Kategori" name="category" required :options="$categories" :value="$expense->category ?? ''" />
</div>

<div class="form-row">
    <x-input label="Tanggal" name="date" type="date" :value="$expense?->date?->format('Y-m-d') ?? now()->format('Y-m-d')" required />
    <x-input label="Jumlah (Rp)" name="amount" type="number" min="0" :value="$expense->amount ?? ''" required />
</div>

<x-textarea label="Deskripsi" name="description" :value="$expense->description ?? ''" required />

<div class="form-row">
    <x-select label="Metode Pembayaran" name="payment_method" :options="['cash' => 'Tunai', 'bank_transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'e_wallet' => 'E-Wallet', 'virtual_account' => 'Virtual Account', 'other' => 'Lainnya']" :value="$expense->payment_method ?? ''" />
    <x-input label="Vendor/Toko" name="vendor" :value="$expense->vendor ?? ''" />
</div>

<x-input label="Lampiran (Bukti)" name="attachment" type="file" accept="image/*,application/pdf" />

<x-textarea label="Catatan" name="notes" :value="$expense->notes ?? ''" />
