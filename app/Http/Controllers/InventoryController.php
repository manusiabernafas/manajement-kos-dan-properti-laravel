<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\StoreInventoryRequest;
use App\Models\Inventory;
use App\Models\Property;
use App\Models\Room;
use App\Services\AuditLogService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Inventory::class);

        $inventories = Inventory::query()
            ->with(['property', 'room'])
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%")->orWhere('inventory_code', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->property_id, fn ($q) => $q->where('property_id', $request->property_id))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('inventories.index', [
            'title' => 'Inventaris',
            'inventories' => $inventories,
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Inventory::class);

        return view('inventories.create', [
            'title' => 'Tambah Inventaris',
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
            'rooms' => Room::orderBy('room_number')->get(),
        ]);
    }

    public function store(StoreInventoryRequest $request): RedirectResponse
    {
        $inventory = Inventory::create($request->validated() + [
            'inventory_code' => NumberGeneratorService::generate('inventories', 'inventory_code', 'INV-AST-'),
        ]);

        $inventory->histories()->create([
            'event_type' => 'created',
            'to_value' => $inventory->status,
            'created_by' => $request->user()->id,
        ]);

        AuditLogService::log('create', 'inventories', $inventory->id, null, $inventory->toArray());

        return redirect()->route('inventories.show', $inventory)->with('success', 'Inventaris berhasil ditambahkan.');
    }

    public function show(Inventory $inventory): View
    {
        $this->authorize('view', $inventory);

        $inventory->load(['property', 'room', 'histories.creator']);

        return view('inventories.show', ['title' => $inventory->name, 'inventory' => $inventory]);
    }

    public function edit(Inventory $inventory): View
    {
        $this->authorize('update', $inventory);

        return view('inventories.edit', [
            'title' => 'Edit Inventaris',
            'inventory' => $inventory,
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
            'rooms' => Room::orderBy('room_number')->get(),
        ]);
    }

    public function update(StoreInventoryRequest $request, Inventory $inventory): RedirectResponse
    {
        $old = $inventory->toArray();
        $validated = $request->validated();

        if ($old['status'] !== $validated['status']) {
            $inventory->histories()->create([
                'event_type' => 'status_change',
                'from_value' => $old['status'],
                'to_value' => $validated['status'],
                'created_by' => $request->user()->id,
            ]);
        }

        if ($old['condition'] !== $validated['condition']) {
            $inventory->histories()->create([
                'event_type' => 'condition_change',
                'from_value' => $old['condition'],
                'to_value' => $validated['condition'],
                'created_by' => $request->user()->id,
            ]);
        }

        $inventory->update($validated);

        AuditLogService::log('update', 'inventories', $inventory->id, $old, $inventory->toArray());

        return redirect()->route('inventories.show', $inventory)->with('success', 'Inventaris berhasil diperbarui.');
    }

    public function destroy(Inventory $inventory): RedirectResponse
    {
        $this->authorize('delete', $inventory);

        $inventory->delete();

        AuditLogService::log('delete', 'inventories', $inventory->id, $inventory->toArray(), null);

        return redirect()->route('inventories.index')->with('success', 'Inventaris berhasil dihapus.');
    }
}
