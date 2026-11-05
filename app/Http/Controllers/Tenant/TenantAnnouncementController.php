<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantAnnouncementController extends PortalController
{
    public function index(Request $request): View
    {
        $tenant = $this->tenant($request);
        $room = $tenant->activeContract?->room;

        $announcements = Announcement::active()
            ->where(function ($q) use ($room) {
                $q->where('target_type', 'all');
                if ($room) {
                    $q->orWhere(fn ($sub) => $sub->where('target_type', 'property')->where('property_id', $room->property_id))
                        ->orWhere(fn ($sub) => $sub->where('target_type', 'building')->where('building_id', $room->building_id))
                        ->orWhere(fn ($sub) => $sub->where('target_type', 'floor')->where('floor_id', $room->floor_id))
                        ->orWhere(fn ($sub) => $sub->where('target_type', 'room')->where('room_id', $room->id));
                }
            })
            ->latest()->paginate(10);

        return view('tenant.announcements.index', ['title' => 'Pengumuman', 'announcements' => $announcements]);
    }

    public function show(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->reads()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['read_at' => now()]
        );

        return redirect()->route('tenant.announcements.index')->with('success', 'Pengumuman ditandai sudah dibaca.');
    }
}
