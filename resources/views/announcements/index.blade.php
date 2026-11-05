<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Pengumuman']]">
    <x-page-header title="Pengumuman" description="Kelola pengumuman untuk penghuni">
        <x-slot:actions>
            @can('announcements.create')
                <a href="{{ route('announcements.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Pengumuman</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        @if($announcements->isEmpty())
            <x-empty-state icon="fa-solid fa-bullhorn" title="Belum ada pengumuman" description="Buat pengumuman pertama untuk penghuni.">
                <x-slot:action>
                    @can('announcements.create')
                        <a href="{{ route('announcements.create') }}" class="btn btn-primary">+ Buat Pengumuman</a>
                    @endcan
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Judul</th><th>Target</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($announcements as $announcement)
                            <tr>
                                <td class="table-cell-primary">{{ $announcement->title }}</td>
                                <td>{{ ucfirst($announcement->target_type) }}</td>
                                <td><x-status-badge :status="$announcement->status" :colors="['draft' => 'muted', 'published' => 'success', 'archived' => 'warning']" /></td>
                                <td class="text-right"><a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-outline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $announcements->links() }}
        @endif
    </div>
</x-layouts.app>
