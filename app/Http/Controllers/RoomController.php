<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Floor;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Room::class);

        $rooms = Room::query()
            ->with(['property', 'building', 'floor', 'roomType', 'activeContract.tenant'])
            ->when($request->q, fn ($q) => $q->where(fn ($sub) => $sub
                ->where('room_number', 'like', "%{$request->q}%")->orWhere('room_name', 'like', "%{$request->q}%")))
            ->when($request->property_id, fn ($q) => $q->where('property_id', $request->property_id))
            ->when($request->building_id, fn ($q) => $q->where('building_id', $request->building_id))
            ->when($request->floor_id, fn ($q) => $q->where('floor_id', $request->floor_id))
            ->when($request->room_type_id, fn ($q) => $q->where('room_type_id', $request->room_type_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('room_number')
            ->get();

        return view('rooms.index', [
            'title' => 'Kamar',
            'rooms' => $rooms,
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
            'buildings' => Building::active()->orderBy('name')->pluck('name', 'id'),
            'floors' => Floor::active()->orderBy('floor_number')->get(),
            'roomTypes' => RoomType::active()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Room::class);

        return view('rooms.create', [
            'title' => 'Tambah Kamar',
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
            'buildings' => Building::active()->get(),
            'floors' => Floor::active()->get(),
            'roomTypes' => RoomType::active()->get(),
            'facilities' => Facility::active()->orderBy('name')->get(),
            'selected' => $request->only(['property_id', 'building_id', 'floor_id']),
        ]);
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $room = Room::create($request->safe()->except('facilities'));

        if ($request->facilities) {
            $room->facilities()->sync($request->facilities);
        }

        AuditLogService::log('create', 'rooms', $room->id, null, $room->toArray());

        return redirect()->route('rooms.show', $room)->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function show(Room $room): View
    {
        $this->authorize('view', $room);

        $room->load([
            'property', 'building', 'floor', 'roomType', 'facilities',
            'activeContract.tenant', 'contracts.tenant',
            'invoices' => fn ($q) => $q->latest()->limit(10),
            'maintenanceTickets' => fn ($q) => $q->latest()->limit(10),
            'complaints' => fn ($q) => $q->latest()->limit(10),
            'inventories', 'meters.readings',
        ]);

        return view('rooms.show', ['title' => $room->room_number, 'room' => $room]);
    }

    public function edit(Room $room): View
    {
        $this->authorize('update', $room);

        $room->load('facilities');

        return view('rooms.edit', [
            'title' => 'Edit Kamar',
            'room' => $room,
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
            'buildings' => Building::active()->get(),
            'floors' => Floor::active()->get(),
            'roomTypes' => RoomType::active()->get(),
            'facilities' => Facility::active()->orderBy('name')->get(),
            'selectedFacilities' => $room->facilities->pluck('id')->toArray(),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $old = $room->toArray();
        $room->update($request->safe()->except('facilities'));
        $room->facilities()->sync($request->facilities ?? []);

        AuditLogService::log('update', 'rooms', $room->id, $old, $room->toArray());

        return redirect()->route('rooms.show', $room)->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        if ($room->status === 'occupied' || $room->contracts()->where('status', 'active')->exists()) {
            return back()->with('error', 'Kamar tidak dapat dihapus karena masih memiliki penghuni aktif.');
        }

        $room->delete();

        AuditLogService::log('delete', 'rooms', $room->id, $room->toArray(), null);

        return redirect()->route('rooms.index')->with('success', 'Kamar berhasil dihapus.');
    }
}
