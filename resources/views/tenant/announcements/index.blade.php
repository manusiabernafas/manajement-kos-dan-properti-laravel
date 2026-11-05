<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header title="Pengumuman" />

    @if($announcements->isEmpty())
        <x-empty-state icon="fa-solid fa-bullhorn" title="Belum ada pengumuman" />
    @else
        @foreach($announcements as $announcement)
            <div class="card mb-16">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-12">
                        <h3 class="font-semibold">{{ $announcement->title }}</h3>
                        <span class="text-xs text-muted">{{ $announcement->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm" style="white-space: pre-line;">{{ $announcement->content }}</p>
                    <form action="{{ route('tenant.announcements.show', $announcement) }}" method="POST" class="mt-16">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Tandai Sudah Dibaca</button>
                    </form>
                </div>
            </div>
        @endforeach
        {{ $announcements->links() }}
    @endif
</x-layouts.app>
