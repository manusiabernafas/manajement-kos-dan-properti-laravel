<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Check-out']]">
    <x-page-header title="Check-out Penghuni" description="Sistem akan menghitung otomatis piutang, denda, dan refund deposit" />

    <div class="card" style="max-width: 760px;">
        <div class="card-body">
            <form action="{{ route('check-outs.store') }}" method="POST">
                @csrf

                <x-select label="Kontrak Aktif" name="contract_id" id="contract_id" required
                    :options="$contracts->mapWithKeys(fn($c) => [$c->id => $c->contract_number.' — '.$c->tenant->full_name.' ('.$c->room->room_number.')'])"
                    :value="$selectedContract->id ?? ''" />

                <x-input label="Tanggal Checkout" name="checkout_date" type="date" :value="now()->format('Y-m-d')" required />

                <div class="form-row">
                    <x-input label="Biaya Kerusakan (Rp)" name="damage_cost" type="number" min="0" value="0" />
                    <x-input label="Denda Tambahan (Rp)" name="penalty_amount" type="number" min="0" value="0" />
                </div>

                <x-input label="Alasan Checkout" name="reason" />
                <x-textarea label="Catatan" name="notes" />

                <x-alert variant="warning" class="mb-16">
                    Piutang tagihan yang belum lunas akan otomatis dipotong dari deposit. Sisa deposit akan dikembalikan ke penghuni.
                </x-alert>

                <div class="form-actions">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Proses Check-out</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
