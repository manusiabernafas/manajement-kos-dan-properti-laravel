<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Ajukan Pembayaran" />

    @if($invoices->isEmpty())
        <x-empty-state icon="fa-solid fa-circle-check" title="Tidak ada tagihan yang perlu dibayar" description="Semua tagihan Anda sudah lunas." />
    @else
        <div class="card" style="max-width: 700px;">
            <div class="card-body">
                <form action="{{ route('tenant.payments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <x-select label="Pilih Tagihan" name="invoice_id" id="invoice_id" required
                        :options="$invoices->mapWithKeys(fn($i) => [$i->id => $i->invoice_number.' (Sisa: Rp '.number_format($i->remaining_amount,0,',','.').')'])"
                        :value="$selectedInvoice->id ?? ''" />

                    <div class="form-row">
                        <x-input label="Jumlah Bayar (Rp)" name="amount" type="number" min="1" :value="$selectedInvoice->remaining_amount ?? ''" required />
                        <x-input label="Tanggal Bayar" name="payment_date" type="date" :value="now()->format('Y-m-d')" required />
                    </div>

                    <x-select label="Metode Pembayaran" name="payment_method" required :options="['cash' => 'Tunai', 'bank_transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'e_wallet' => 'E-Wallet', 'virtual_account' => 'Virtual Account', 'other' => 'Lainnya']" />
                    <x-input label="Nomor Referensi" name="reference_number" hint="Opsional" />
                    <x-input label="Bukti Transfer" name="proof" type="file" accept="image/*" required />
                    <x-textarea label="Catatan" name="notes" />

                    <div class="form-actions">
                        <a href="{{ route('tenant.invoices.index') }}" class="btn btn-secondary">Batal</a>
                        <x-button type="submit" variant="primary">Kirim Bukti Pembayaran</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-layouts.app>
