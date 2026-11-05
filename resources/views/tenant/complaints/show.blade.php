<x-layouts.app :title="$title" :sidebar-menu="config('tenant_sidebar')">
    <x-page-header :title="$complaint->title" :description="$complaint->ticket_number">
        <x-slot:actions>
            <x-status-badge :status="$complaint->status" :colors="\App\Models\Complaint::STATUS_COLORS" />
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-24">
        <div class="card-body">
            <p class="text-sm">{{ $complaint->description }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Komentar</h3></div>
        <div class="card-body">
            @forelse($complaint->comments as $comment)
                <div class="widget-item" style="padding-left:0;padding-right:0;">
                    <div class="widget-item-icon" style="background:var(--color-primary-soft);color:var(--color-primary);"><i class="fa-solid fa-comment"></i></div>
                    <div class="widget-item-body">
                        <div class="widget-item-title">{{ $comment->user->name }}</div>
                        <div class="widget-item-desc">{{ $comment->comment }}</div>
                        <div class="text-xs text-muted mt-8">{{ $comment->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-sm">Belum ada komentar dari petugas.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>
