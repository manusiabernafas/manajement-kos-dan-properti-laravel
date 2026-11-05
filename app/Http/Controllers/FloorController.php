<?php

namespace App\Http\Controllers;

use App\Http\Requests\Floor\StoreFloorRequest;
use App\Http\Requests\Floor\UpdateFloorRequest;
use App\Models\Building;
use App\Models\Floor;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FloorController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Floor::class);

        $floors = Floor::query()
            ->with('building.property')
            ->withCount('rooms')
            ->when($request->building_id, fn ($q) => $q->where('building_id', $request->building_id))
            ->orderBy('floor_number')
            ->paginate(15)
            ->withQueryString();

        return view('floors.index', [
            'title' => 'Lantai',
            'floors' => $floors,
            'buildings' => Building::active()->with('property')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Floor::class);

        return view('floors.create', [
            'title' => 'Tambah Lantai',
            'buildings' => Building::active()->with('property')->get()->mapWithKeys(fn ($b) => [$b->id => "{$b->property->name} — {$b->name}"]),
            'selectedBuildingId' => $request->building_id,
        ]);
    }

    public function store(StoreFloorRequest $request): RedirectResponse
    {
        $floor = Floor::create($request->validated());

        AuditLogService::log('create', 'floors', $floor->id, null, $floor->toArray());

        return redirect()->route('floors.show', $floor)->with('success', 'Lantai berhasil ditambahkan.');
    }

    public function show(Floor $floor): View
    {
        $this->authorize('view', $floor);

        $floor->load(['building.property', 'rooms.roomType']);

        return view('floors.show', ['title' => $floor->name ?? 'Lantai '.$floor->floor_number, 'floor' => $floor]);
    }

    public function edit(Floor $floor): View
    {
        $this->authorize('update', $floor);

        return view('floors.edit', [
            'title' => 'Edit Lantai',
            'floor' => $floor,
            'buildings' => Building::active()->with('property')->get()->mapWithKeys(fn ($b) => [$b->id => "{$b->property->name} — {$b->name}"]),
        ]);
    }

    public function update(UpdateFloorRequest $request, Floor $floor): RedirectResponse
    {
        $old = $floor->toArray();
        $floor->update($request->validated());

        AuditLogService::log('update', 'floors', $floor->id, $old, $floor->toArray());

        return redirect()->route('floors.show', $floor)->with('success', 'Lantai berhasil diperbarui.');
    }

    public function destroy(Floor $floor): RedirectResponse
    {
        $this->authorize('delete', $floor);

        if ($floor->rooms()->exists()) {
            return back()->with('error', 'Lantai tidak dapat dihapus karena masih memiliki data kamar.');
        }

        $floor->delete();

        AuditLogService::log('delete', 'floors', $floor->id, $floor->toArray(), null);

        return redirect()->route('floors.index')->with('success', 'Lantai berhasil dihapus.');
    }
}
