<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use App\Models\PenaltyConfig;
use App\Models\Property;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenaltyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Penalty::class);

        $penalties = Penalty::query()
            ->with(['invoice.tenant', 'invoice.room'])
            ->orderByDesc('applied_date')
            ->paginate(15);

        $configs = PenaltyConfig::with('property')->orderByRaw('property_id IS NULL DESC')->get();

        return view('penalties.index', [
            'title' => 'Denda',
            'penalties' => $penalties,
            'configs' => $configs,
            'properties' => Property::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function storeConfig(Request $request): RedirectResponse
    {
        $this->authorize('create', Penalty::class);

        $validated = $request->validate([
            'property_id' => ['nullable', 'exists:properties,id'],
            'calculation_type' => ['required', 'in:fixed,per_day,percentage'],
            'amount' => ['required', 'numeric', 'min:0'],
            'grace_period_days' => ['required', 'integer', 'min:0', 'max:90'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $config = PenaltyConfig::create($validated);

        AuditLogService::log('create', 'penalties', $config->id, null, $config->toArray());

        return back()->with('success', 'Konfigurasi denda berhasil ditambahkan.');
    }

    public function updateConfig(Request $request, PenaltyConfig $config): RedirectResponse
    {
        $this->authorize('update', Penalty::class);

        $validated = $request->validate([
            'property_id' => ['nullable', 'exists:properties,id'],
            'calculation_type' => ['required', 'in:fixed,per_day,percentage'],
            'amount' => ['required', 'numeric', 'min:0'],
            'grace_period_days' => ['required', 'integer', 'min:0', 'max:90'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $old = $config->toArray();
        $config->update($validated);

        AuditLogService::log('update', 'penalties', $config->id, $old, $config->toArray());

        return back()->with('success', 'Konfigurasi denda berhasil diperbarui.');
    }

    public function destroyConfig(PenaltyConfig $config): RedirectResponse
    {
        $this->authorize('delete', Penalty::class);

        $config->delete();

        return back()->with('success', 'Konfigurasi denda berhasil dihapus.');
    }
}
