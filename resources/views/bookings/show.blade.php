<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Booking', 'url' => route('bookings.index')], ['label' => $booking->booking_number]]">
    <x-page-header :title="$booking->booking_number" :description="$booking->tenant->full_name">
        <x-slot:actions>
            <x-status-badge :status="$booking->status" :colors="\App\Models\Booking::STATUS_COLORS" />
            @if(in_array($booking->status, ['inquiry', 'reserved']))
                @can('bookings.confirm')
                    <form action="{{ route('bookings.confirm', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline"><i class="fa-solid fa-check"></i> Konfirmasi</button>
                    </form>
                @endcan
            @endif
            @if($booking->status === 'confirmed')
                @can('check-ins.create')
                    <a href="{{ route('check-ins.create', ['booking_id' => $booking->id]) }}" class="btn btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Proses Check-in</a>
                @endcan
            @endif
            @if(!in_array($booking->status, ['checked_in', 'cancelled']))
                @can('bookings.update')
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" data-confirm-submit data-confirm-message="Batalkan booking ini?">
                        @csrf
                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Batalkan</button>
                    </form>
                @endcan
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-3">
        <div class="card">
            <div class="card-header"><h3>Informasi Booking</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Tanggal Booking</span><span>{{ $booking->booking_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Rencana Check-in</span><span>{{ $booking->check_in_date->format('d M Y') }}</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Harga Bulanan</span><span>Rp {{ number_format($booking->monthly_price, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Deposit</span><span>Rp {{ number_format($booking->deposit, 0, ',', '.') }}</span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Calon Penghuni</h3></div>
            <div class="card-body">
                <p class="font-semibold">{{ $booking->tenant->full_name }}</p>
                <p class="text-muted text-sm mt-8">{{ $booking->tenant->phone }}</p>
                <a href="{{ route('tenants.show', $booking->tenant) }}" class="btn btn-outline btn-sm mt-16">Lihat Profil</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Kamar</h3></div>
            <div class="card-body">
                <p class="font-semibold">{{ $booking->room->room_number }}</p>
                <p class="text-muted text-sm mt-8">{{ $booking->room->property->name }}</p>
                <a href="{{ route('rooms.show', $booking->room) }}" class="btn btn-outline btn-sm mt-16">Lihat Kamar</a>
            </div>
        </div>
    </div>

    @if($booking->notes)
        <div class="card mt-24">
            <div class="card-header"><h3>Catatan</h3></div>
            <div class="card-body"><p class="text-sm">{{ $booking->notes }}</p></div>
        </div>
    @endif
</x-layouts.app>
