<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\AuditLogService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = Booking::query()
            ->with(['tenant', 'room.property'])
            ->when($request->q, fn ($q) => $q->where('booking_number', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('booking_date')
            ->paginate(15)
            ->withQueryString();

        return view('bookings.index', ['title' => 'Booking', 'bookings' => $bookings]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Booking::class);

        return view('bookings.create', [
            'title' => 'Tambah Booking',
            'tenants' => Tenant::orderBy('full_name')->get(['id', 'full_name', 'tenant_code']),
            'rooms' => Room::where('status', 'available')->with('roomType')->get(),
            'selectedRoomId' => $request->room_id,
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $booking = Booking::create($request->validated() + [
            'booking_number' => NumberGeneratorService::generate('bookings', 'booking_number', NumberGeneratorService::bookingPrefix()),
            'status' => 'inquiry',
            'created_by' => $request->user()->id,
        ]);

        AuditLogService::log('create', 'bookings', $booking->id, null, $booking->toArray());

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking berhasil dibuat.');
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['tenant', 'room.property', 'checkIn']);

        return view('bookings.show', ['title' => $booking->booking_number, 'booking' => $booking]);
    }

    public function edit(Booking $booking): View
    {
        $this->authorize('update', $booking);

        return view('bookings.edit', [
            'title' => 'Edit Booking',
            'booking' => $booking,
            'tenants' => Tenant::orderBy('full_name')->get(['id', 'full_name', 'tenant_code']),
            'rooms' => Room::whereIn('status', ['available', 'reserved'])->orWhere('id', $booking->room_id)->with('roomType')->get(),
        ]);
    }

    public function update(StoreBookingRequest $request, Booking $booking): RedirectResponse
    {
        $old = $booking->toArray();
        $booking->update($request->validated());

        AuditLogService::log('update', 'bookings', $booking->id, $old, $booking->toArray());

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking berhasil diperbarui.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $this->authorize('delete', $booking);

        if ($booking->status === 'checked_in') {
            return back()->with('error', 'Booking yang sudah check-in tidak dapat dihapus.');
        }

        $booking->delete();

        AuditLogService::log('delete', 'bookings', $booking->id, $booking->toArray(), null);

        return redirect()->route('bookings.index')->with('success', 'Booking berhasil dihapus.');
    }

    public function confirm(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        abort_unless($request->user()->can('bookings.confirm'), 403);

        if (! in_array($booking->status, ['inquiry', 'reserved'], true)) {
            return back()->with('error', 'Hanya booking berstatus Inquiry/Reserved yang dapat dikonfirmasi.');
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'confirmed']);
            $booking->room->update(['status' => 'reserved']);
        });

        AuditLogService::log('update', 'bookings', $booking->id, null, ['status' => 'confirmed']);

        return back()->with('success', 'Booking berhasil dikonfirmasi. Kamar telah direservasi.');
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);

        if ($booking->status === 'checked_in') {
            return back()->with('error', 'Booking yang sudah check-in tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);
            if ($booking->room->status === 'reserved') {
                $booking->room->update(['status' => 'available']);
            }
        });

        AuditLogService::log('update', 'bookings', $booking->id, null, ['status' => 'cancelled']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
