<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengumuman', 'url' => route('announcements.index')], ['label' => $announcement->title]]">
    <x-page-header :title="$announcement->title" :description="'Target: '.ucfirst($announcement->target_type)">
        <x-slot:actions>
            <x-status-badge :status="$announcement->status" :colors="['draft' => 'muted', 'published' => 'success', 'archived' => 'warning']" />
            @can('announcements.update')
                <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-3">
        <div class="card" style="grid-column: span 2;">
            <div class="card-header"><h3>Isi Pengumuman</h3></div>
            <div class="card-body">
                <p class="text-sm" style="white-space: pre-line;">{{ $announcement->content }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Statistik</h3></div>
            <div class="card-body">
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Dibaca oleh</span><span class="font-semibold">{{ $readCount }} orang</span></div>
                <div class="flex justify-between mb-12"><span class="text-muted text-sm">Publish</span><span>{{ $announcement->publish_at?->format('d M Y H:i') ?? 'Segera' }}</span></div>
                <div class="flex justify-between"><span class="text-muted text-sm">Berakhir</span><span>{{ $announcement->expired_at?->format('d M Y H:i') ?? '-' }}</span></div>
            </div>
        </div>
    </div>
</x-layouts.app>
