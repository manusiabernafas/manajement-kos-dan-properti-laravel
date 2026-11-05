<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Check-in']]">
    <x-page-header title="Check-in Penghuni" description="Proses check-in akan membuat kontrak, deposit, dan tagihan pertama secara otomatis" />

    <div class="card" style="max-width: 760px;">
        <div class="card-body">
            <form action="{{ route('check-ins.store') }}" method="POST">
                @csrf
                @if($booking)
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <x-alert variant="info" class="mb-24">Check-in dari booking {{ $booking->booking_number }} — {{ $booking->tenant->full_name }}</x-alert>
                @endif

                <div class="form-row">
                    <x-select label="Penghuni" name="tenant_id" required
                        :options="$tenants->mapWithKeys(fn($t) => [$t->id => $t->full_name.' ('.$t->tenant_code.')'])"
                        :value="$booking->tenant_id ?? ''" />
                    <x-select label="Kamar" name="room_id" id="room_id" required
                        :options="$rooms->mapWithKeys(fn($r) => [$r->id => $r->room_number.' — '.$r->roomType->name])"
                        :value="$selectedRoom->id ?? $booking->room_id ?? ''" />
                </div>

                <div class="form-row">
                    <x-input label="Tanggal Check-in" name="check_in_date" type="date" :value="now()->format('Y-m-d')" required />
                    <x-input label="Tanggal Billing Berikutnya" name="billing_date" id="billing_date" type="date" required hint="Tanggal jatuh tempo tagihan pertama" />
                </div>

                <div class="form-row">
                    <x-input label="Harga Bulanan (Rp)" name="monthly_price" id="monthly_price" type="number" min="0" :value="$booking->monthly_price ?? $selectedRoom->price ?? ''" required />
                    <x-input label="Deposit (Rp)" name="deposit" id="deposit_amount" type="number" min="0" :value="$booking->deposit ?? $selectedRoom->deposit ?? ''" required />
                </div>

                <x-input label="Durasi Kontrak (bulan)" name="duration_months" type="number" min="1" max="36" value="12" required />

                <x-textarea label="Syarat & Ketentuan Kontrak" name="terms" />
                <x-textarea label="Catatan" name="notes" />

                <div class="form-actions">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Proses Check-in</x-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        var roomsData = @json($rooms->mapWithKeys(fn($r) => [$r->id => ['price' => (float) $r->price, 'deposit' => (float) $r->deposit]]));
        document.getElementById('room_id').addEventListener('change', function() {
            var data = roomsData[this.value];
            if (data) {
                document.getElementById('monthly_price').value = data.price;
                document.getElementById('deposit_amount').value = data.deposit;
            }
        });
    </script>
</x-layouts.app>
