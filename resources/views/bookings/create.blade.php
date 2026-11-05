<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Booking', 'url' => route('bookings.index')], ['label' => 'Tambah']]">
    <x-page-header title="Tambah Booking" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('bookings.store') }}" method="POST" id="booking-form">
                @csrf
                <x-select label="Calon Penghuni" name="tenant_id" required :options="$tenants->mapWithKeys(fn($t) => [$t->id => $t->full_name.' ('.$t->tenant_code.')'])" />
                <x-select label="Kamar" name="room_id" id="room_id" required :options="$rooms->mapWithKeys(fn($r) => [$r->id => $r->room_number.' — '.$r->roomType->name.' (Rp '.number_format($r->price,0,',','.').')'])" :value="$selectedRoomId" />

                <div class="form-row">
                    <x-input label="Tanggal Booking" name="booking_date" type="date" :value="now()->format('Y-m-d')" required />
                    <x-input label="Tanggal Rencana Check-in" name="check_in_date" type="date" required />
                </div>
                <x-input label="Rencana Checkout (opsional)" name="expected_checkout_date" type="date" />

                <div class="form-row">
                    <x-input label="Harga Bulanan (Rp)" name="monthly_price" id="monthly_price" type="number" min="0" required />
                    <x-input label="Deposit (Rp)" name="deposit" id="deposit_amount" type="number" min="0" required />
                </div>

                <x-textarea label="Catatan" name="notes" />

                <div class="form-actions">
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Booking</x-button>
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
        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById('room_id').dispatchEvent(new Event('change'));
        });
    </script>
</x-layouts.app>
