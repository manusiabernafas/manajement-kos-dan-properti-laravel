<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pembayaran', 'url' => route('payments.index')], ['label' => 'Catat Pembayaran']]">
    <x-page-header title="Catat Pembayaran" />

    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <x-select label="Tagihan" name="invoice_id" id="invoice_id" required
                    :options="$invoices->mapWithKeys(fn($i) => [$i->id => $i->invoice_number.' — '.$i->tenant->full_name.' (Sisa: Rp '.number_format($i->remaining_amount,0,',','.').')'])"
                    :value="$selectedInvoice->id ?? ''" />

                <div class="form-row">
                    <x-input label="Jumlah (Rp)" name="amount" type="number" min="1" :value="$selectedInvoice->remaining_amount ?? ''" required />
                    <x-input label="Tanggal Pembayaran" name="payment_date" type="date" :value="now()->format('Y-m-d')" required />
                </div>

                <x-select label="Metode Pembayaran" name="payment_method" required :options="['cash' => 'Tunai', 'bank_transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'e_wallet' => 'E-Wallet', 'virtual_account' => 'Virtual Account', 'other' => 'Lainnya']" />

                <x-input label="Nomor Referensi" name="reference_number" hint="Opsional, nomor transaksi/referensi" />
                <x-input label="Bukti Pembayaran" name="proof" type="file" accept="image/*" />
                <x-textarea label="Catatan" name="notes" />

                <div class="form-actions">
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Pembayaran</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
