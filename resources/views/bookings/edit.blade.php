<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Booking', 'url' => route('bookings.index')], ['label' => $booking->booking_number, 'url' => route('bookings.show', $booking)], ['label' => 'Edit']]">
    <x-page-header title="Edit Booking" :description="$booking->booking_number" />
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form action="{{ route('bookings.update', $booking) }}" method="POST">
                @csrf
                @method('PUT')
                <x-select label="Calon Penghuni" name="tenant_id" required :options="$tenants->mapWithKeys(fn($t) => [$t->id => $t->full_name.' ('.$t->tenant_code.')'])" :value="$booking->tenant_id" />
                <x-select label="Kamar" name="room_id" required :options="$rooms->mapWithKeys(fn($r) => [$r->id => $r->room_number])" :value="$booking->room_id" />
                <div class="form-row">
                    <x-input label="Tanggal Booking" name="booking_date" type="date" :value="$booking->booking_date->format('Y-m-d')" required />
                    <x-input label="Rencana Check-in" name="check_in_date" type="date" :value="$booking->check_in_date->format('Y-m-d')" required />
                </div>
                <x-input label="Rencana Checkout" name="expected_checkout_date" type="date" :value="$booking->expected_checkout_date?->format('Y-m-d')" />
                <div class="form-row">
                    <x-input label="Harga Bulanan (Rp)" name="monthly_price" type="number" min="0" :value="$booking->monthly_price" required />
                    <x-input label="Deposit (Rp)" name="deposit" type="number" min="0" :value="$booking->deposit" required />
                </div>
                <x-textarea label="Catatan" name="notes" :value="$booking->notes" />
                <div class="form-actions">
                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">Batal</a>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
