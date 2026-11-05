<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomType\StoreRoomTypeRequest;
use App\Http\Requests\RoomType\UpdateRoomTypeRequest;
use App\Models\Facility;
use App\Models\Property;
use App\Models\RoomType;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', RoomType::class);

        $roomTypes = RoomType::query()
            ->with('property')
            ->withCount('rooms')
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->when($request->property_id, fn ($q) => $q->where('property_id', $request->property_id))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('room-types.index', [
            'title' => 'Tipe Kamar',
            'roomTypes' => $roomTypes,
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', RoomType::class);

        return view('room-types.create', [
            'title' => 'Tambah Tipe Kamar',
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
            'facilities' => Facility::active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreRoomTypeRequest $request): RedirectResponse
    {
        $roomType = RoomType::create($request->safe()->except('facilities'));

        if ($request->facilities) {
            $roomType->facilities()->sync($request->facilities);
        }

        AuditLogService::log('create', 'room-types', $roomType->id, null, $roomType->toArray());

        return redirect()->route('room-types.show', $roomType)->with('success', 'Tipe kamar berhasil ditambahkan.');
    }

    public function show(RoomType $roomType): View
    {
        $this->authorize('view', $roomType);

        $roomType->load(['property', 'facilities', 'rooms']);

        return view('room-types.show', ['title' => $roomType->name, 'roomType' => $roomType]);
    }

    public function edit(RoomType $roomType): View
    {
        $this->authorize('update', $roomType);

        $roomType->load('facilities');

        return view('room-types.edit', [
            'title' => 'Edit Tipe Kamar',
            'roomType' => $roomType,
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
            'facilities' => Facility::active()->orderBy('name')->get(),
            'selectedFacilities' => $roomType->facilities->pluck('id')->toArray(),
        ]);
    }

    public function update(UpdateRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $old = $roomType->toArray();
        $roomType->update($request->safe()->except('facilities'));
        $roomType->facilities()->sync($request->facilities ?? []);

        AuditLogService::log('update', 'room-types', $roomType->id, $old, $roomType->toArray());

        return redirect()->route('room-types.show', $roomType)->with('success', 'Tipe kamar berhasil diperbarui.');
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        $this->authorize('delete', $roomType);

        if ($roomType->rooms()->exists()) {
            return back()->with('error', 'Tipe kamar tidak dapat dihapus karena masih digunakan oleh kamar.');
        }

        $roomType->delete();

        AuditLogService::log('delete', 'room-types', $roomType->id, $roomType->toArray(), null);

        return redirect()->route('room-types.index')->with('success', 'Tipe kamar berhasil dihapus.');
    }
}
