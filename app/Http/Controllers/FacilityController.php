<?php

namespace App\Http\Controllers;

use App\Http\Requests\Facility\StoreFacilityRequest;
use App\Http\Requests\Facility\UpdateFacilityRequest;
use App\Models\Facility;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Facility::class);

        $facilities = Facility::query()
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->orderBy('category')->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('facilities.index', [
            'title' => 'Fasilitas',
            'facilities' => $facilities,
            'categories' => Facility::query()->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function store(StoreFacilityRequest $request): RedirectResponse
    {
        $facility = Facility::create($request->validated());

        AuditLogService::log('create', 'facilities', $facility->id, null, $facility->toArray());

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function update(UpdateFacilityRequest $request, Facility $facility): RedirectResponse
    {
        $old = $facility->toArray();
        $facility->update($request->validated());

        AuditLogService::log('update', 'facilities', $facility->id, $old, $facility->toArray());

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        $this->authorize('delete', $facility);

        $facility->delete();

        AuditLogService::log('delete', 'facilities', $facility->id, $facility->toArray(), null);

        return redirect()->route('facilities.index')->with('success', 'Fasilitas berhasil dihapus.');
    }
}
