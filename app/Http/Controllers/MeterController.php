<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Room;
use App\Services\AuditLogService;
use App\Services\MeterReadingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class MeterController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Meter::class);

        $meters = Meter::query()
            ->with(['room.property'])
            ->when($request->meter_type, fn ($q) => $q->where('meter_type', $request->meter_type))
            ->when($request->property_id, fn ($q) => $q->whereHas('room', fn ($r) => $r->where('property_id', $request->property_id)))
            ->orderBy('room_id')
            ->paginate(20)
            ->withQueryString();

        return view('meters.index', [
            'title' => 'Meter',
            'meters' => $meters,
            'rooms' => Room::orderBy('room_number')->get(),
            'properties' => \App\Models\Property::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Meter::class);

        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'meter_type' => ['required', 'in:electricity,water'],
            'meter_number' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:20'],
            'tariff' => ['required', 'numeric', 'min:0'],
        ]);

        $exists = Meter::where('room_id', $validated['room_id'])->where('meter_type', $validated['meter_type'])->exists();
        if ($exists) {
            return back()->with('error', 'Kamar ini sudah memiliki meter untuk tipe tersebut.');
        }

        $validated['status'] = 'active';
        $meter = Meter::create($validated);

        AuditLogService::log('create', 'meters', $meter->id, null, $meter->toArray());

        return redirect()->route('meters.show', $meter)->with('success', 'Meter berhasil ditambahkan.');
    }

    public function show(Meter $meter): View
    {
        $this->authorize('view', $meter);

        $meter->load(['room.property', 'readings']);

        return view('meters.show', ['title' => 'Meter '.$meter->room->room_number, 'meter' => $meter]);
    }

    public function storeReading(Request $request, Meter $meter, MeterReadingService $service): RedirectResponse
    {
        $this->authorize('update', $meter);

        $validated = $request->validate([
            'reading_date' => ['required', 'date'],
            'current_value' => ['required', 'numeric', 'min:0'],
            'tariff' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'max:3072'],
            'notes' => ['nullable', 'string'],
            'generate_invoice' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('meter-readings', 'public');
        }

        try {
            $service->recordReading($meter, $validated, $request->user()->id);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pembacaan meter berhasil dicatat.');
    }

    public function destroy(Meter $meter): RedirectResponse
    {
        $this->authorize('delete', $meter);

        $meter->delete();

        return redirect()->route('meters.index')->with('success', 'Meter berhasil dihapus.');
    }
}
