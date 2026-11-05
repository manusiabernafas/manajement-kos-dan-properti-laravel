<?php

namespace App\Http\Controllers;

use App\Http\Requests\Announcement\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Property;
use App\Models\Room;
use App\Services\AuditLogService;
use App\Services\NotificationDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Announcement::class);

        $announcements = Announcement::query()
            ->when($request->q, fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('announcements.index', ['title' => 'Pengumuman', 'announcements' => $announcements]);
    }

    public function create(): View
    {
        $this->authorize('create', Announcement::class);

        return view('announcements.create', [
            'title' => 'Buat Pengumuman',
            'properties' => Property::orderBy('name')->get(),
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('floor_number')->get(),
            'rooms' => Room::orderBy('room_number')->get(),
        ]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $announcement = Announcement::create($request->validated() + ['created_by' => $request->user()->id]);

        AuditLogService::log('create', 'announcements', $announcement->id, null, $announcement->toArray());

        if ($announcement->status === 'published') {
            app(NotificationDispatchService::class)->announcementPublished($announcement);
        }

        return redirect()->route('announcements.show', $announcement)->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(Announcement $announcement): View
    {
        $this->authorize('view', $announcement);

        $announcement->load(['property', 'building', 'floor', 'room', 'creator']);
        $readCount = $announcement->reads()->count();

        return view('announcements.show', ['title' => $announcement->title, 'announcement' => $announcement, 'readCount' => $readCount]);
    }

    public function edit(Announcement $announcement): View
    {
        $this->authorize('update', $announcement);

        return view('announcements.edit', [
            'title' => 'Edit Pengumuman',
            'announcement' => $announcement,
            'properties' => Property::orderBy('name')->get(),
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('floor_number')->get(),
            'rooms' => Room::orderBy('room_number')->get(),
        ]);
    }

    public function update(StoreAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $old = $announcement->toArray();
        $announcement->update($request->validated());

        AuditLogService::log('update', 'announcements', $announcement->id, $old, $announcement->toArray());

        return redirect()->route('announcements.show', $announcement)->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        AuditLogService::log('delete', 'announcements', $announcement->id, $announcement->toArray(), null);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
