<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Booking']]">
    <x-page-header title="Booking" description="Kelola booking calon penghuni">
        <x-slot:actions>
            @can('bookings.create')
                <a href="{{ route('bookings.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Booking</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <form method="GET" class="table-toolbar">
            <div class="table-toolbar-left">
                <div class="table-search">
                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. booking...">
                </div>
                <select name="status" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="inquiry" @selected(request('status') === 'inquiry')>Inquiry</option>
                    <option value="reserved" @selected(request('status') === 'reserved')>Reserved</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
                    <option value="checked_in" @selected(request('status') === 'checked_in')>Checked In</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                </select>
            </div>
        </form>

        @if($bookings->isEmpty())
            <x-empty-state icon="fa-solid fa-calendar-check" title="Belum ada booking" description="Tambahkan booking pertama untuk calon penghuni.">
                <x-slot:action>
                    @can('bookings.create')
                        <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ Tambah Booking</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr><th>No. Booking</th><th>Calon Penghuni</th><th>Kamar</th><th>Tanggal Check-in</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td class="table-cell-primary">{{ $booking->booking_number }}</td>
                                <td>{{ $booking->tenant->full_name }}</td>
                                <td>{{ $booking->room->room_number }}</td>
                                <td>{{ $booking->check_in_date->format('d M Y') }}</td>
                                <td><x-status-badge :status="$booking->status" :colors="\App\Models\Booking::STATUS_COLORS" /></td>
                                <td class="text-right">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $bookings->links() }}
        @endif
    </div>
</x-layouts.app>
