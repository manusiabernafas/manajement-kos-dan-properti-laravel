<?php

namespace App\Http\Controllers;

use App\Http\Requests\Building\StoreBuildingRequest;
use App\Http\Requests\Building\UpdateBuildingRequest;
use App\Models\Building;
use App\Models\Property;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuildingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Building::class);

        $buildings = Building::query()
            ->with('property')
            ->withCount(['floors', 'rooms'])
            ->when($request->q, fn ($q) => $q->where(fn ($sub) => $sub
                ->where('name', 'like', "%{$request->q}%")->orWhere('code', 'like', "%{$request->q}%")))
            ->when($request->property_id, fn ($q) => $q->where('property_id', $request->property_id))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('buildings.index', [
            'title' => 'Gedung',
            'buildings' => $buildings,
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Building::class);

        return view('buildings.create', [
            'title' => 'Tambah Gedung',
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
            'selectedPropertyId' => $request->property_id,
        ]);
    }

    public function store(StoreBuildingRequest $request): RedirectResponse
    {
        $building = Building::create($request->validated());

        AuditLogService::log('create', 'buildings', $building->id, null, $building->toArray());

        return redirect()->route('buildings.show', $building)->with('success', 'Gedung berhasil ditambahkan.');
    }

    public function show(Building $building): View
    {
        $this->authorize('view', $building);

        $building->load(['property', 'floors.rooms']);

        return view('buildings.show', ['title' => $building->name, 'building' => $building]);
    }

    public function edit(Building $building): View
    {
        $this->authorize('update', $building);

        return view('buildings.edit', [
            'title' => 'Edit Gedung',
            'building' => $building,
            'properties' => Property::active()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(UpdateBuildingRequest $request, Building $building): RedirectResponse
    {
        $old = $building->toArray();
        $building->update($request->validated());

        AuditLogService::log('update', 'buildings', $building->id, $old, $building->toArray());

        return redirect()->route('buildings.show', $building)->with('success', 'Gedung berhasil diperbarui.');
    }

    public function destroy(Building $building): RedirectResponse
    {
        $this->authorize('delete', $building);

        if ($building->rooms()->exists()) {
            return back()->with('error', 'Gedung tidak dapat dihapus karena masih memiliki data kamar.');
        }

        $building->delete();

        AuditLogService::log('delete', 'buildings', $building->id, $building->toArray(), null);

        return redirect()->route('buildings.index')->with('success', 'Gedung berhasil dihapus.');
    }
}
