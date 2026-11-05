<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckIn\StoreCheckInRequest;
use App\Models\Booking;
use App\Models\CheckIn;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\CheckInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CheckInController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CheckIn::class);

        $checkIns = CheckIn::query()
            ->with(['tenant', 'room.property', 'contract'])
            ->orderByDesc('check_in_date')
            ->paginate(15);

        return view('check-ins.index', ['title' => 'Check-in', 'checkIns' => $checkIns]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', CheckIn::class);

        $room = $request->room_id ? Room::with('roomType')->find($request->room_id) : null;
        $booking = $request->booking_id ? Booking::with(['tenant', 'room'])->find($request->booking_id) : null;

        return view('check-ins.create', [
            'title' => 'Check-in Penghuni',
            'tenants' => Tenant::orderBy('full_name')->get(['id', 'full_name', 'tenant_code']),
            'rooms' => Room::whereIn('status', ['available', 'reserved'])->with('roomType')->get(),
            'selectedRoom' => $room,
            'booking' => $booking,
        ]);
    }

    public function store(StoreCheckInRequest $request, CheckInService $service): RedirectResponse
    {
        try {
            $contract = $service->process($request->validated(), $request->user()->id);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Check-in berhasil diproses. Kontrak dan tagihan pertama telah dibuat.');
    }

    public function show(CheckIn $checkIn): View
    {
        $this->authorize('view', $checkIn);

        $checkIn->load(['tenant', 'room.property', 'contract', 'booking']);

        return view('check-ins.show', ['title' => 'Detail Check-in', 'checkIn' => $checkIn]);
    }
}
