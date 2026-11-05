<?php

namespace App\Http\Controllers;

use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        $properties = Property::query()
            ->withCount(['buildings', 'rooms'])
            ->when($request->q, fn ($q) => $q->where(fn ($sub) => $sub
                ->where('name', 'like', "%{$request->q}%")
                ->orWhere('code', 'like', "%{$request->q}%")
                ->orWhere('city', 'like', "%{$request->q}%")))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('properties.index', [
            'title' => 'Properti',
            'properties' => $properties,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Property::class);

        return view('properties.create', ['title' => 'Tambah Properti']);
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $property = Property::create($request->validated() + ['created_by' => $request->user()->id]);

        AuditLogService::log('create', 'properties', $property->id, null, $property->toArray());

        return redirect()->route('properties.show', $property)->with('success', 'Properti berhasil ditambahkan.');
    }

    public function show(Property $property): View
    {
        $this->authorize('view', $property);

        $property->load(['buildings.floors', 'creator']);
        $property->loadCount(['rooms', 'buildings']);

        $roomStats = $property->rooms()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('properties.show', [
            'title' => $property->name,
            'property' => $property,
            'roomStats' => $roomStats,
        ]);
    }

    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        return view('properties.edit', ['title' => 'Edit Properti', 'property' => $property]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $old = $property->toArray();
        $property->update($request->validated());

        AuditLogService::log('update', 'properties', $property->id, $old, $property->toArray());

        return redirect()->route('properties.show', $property)->with('success', 'Properti berhasil diperbarui.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        if ($property->rooms()->exists()) {
            return back()->with('error', 'Properti tidak dapat dihapus karena masih memiliki data kamar.');
        }

        $property->delete();

        AuditLogService::log('delete', 'properties', $property->id, $property->toArray(), null);

        return redirect()->route('properties.index')->with('success', 'Properti berhasil dihapus.');
    }
}
