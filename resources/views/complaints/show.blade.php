<x-layouts.app :title="$title" :breadcrumbs="[['label' => 'Komplain', 'url' => route('complaints.index')], ['label' => $complaint->ticket_number]]">
    <x-page-header :title="$complaint->title" :description="$complaint->ticket_number">
        <x-slot:actions>
            <x-status-badge :status="$complaint->priority" :colors="\App\Models\Complaint::PRIORITY_COLORS" />
            <x-status-badge :status="$complaint->status" :colors="\App\Models\Complaint::STATUS_COLORS" />
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-3">
        <div class="card" style="grid-column: span 2;">
            <div class="card-header"><h3>Deskripsi</h3></div>
            <div class="card-body">
                <p class="text-sm">{{ $complaint->description }}</p>
                <div class="grid grid-cols-2 mt-24">
                    <div><div class="text-muted text-sm">Penghuni</div><div class="font-medium">{{ $complaint->tenant->full_name }}</div></div>
                    <div><div class="text-muted text-sm">Kategori</div><div class="font-medium">{{ $complaint->category }}</div></div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Update Status</h3></div>
            <div class="card-body">
                @can('complaints.update')
                    <form action="{{ route('complaints.update', $complaint) }}" method="POST">
                        @csrf @method('PUT')
                        <x-select label="Status" name="status" required :options="['open'=>'Open','assigned'=>'Assigned','in_progress'=>'In Progress','waiting'=>'Waiting','resolved'=>'Resolved','closed'=>'Closed','cancelled'=>'Cancelled']" :value="$complaint->status" />
                        <x-select label="Ditugaskan Kepada" name="assigned_to" :options="$technicians->mapWithKeys(fn($u) => [$u->id => $u->name])" :value="$complaint->assigned_to" />
                        <x-button type="submit" variant="primary" class="btn-block">Perbarui</x-button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <div class="card mt-24">
        <div class="card-header"><h3>Komentar & Riwayat</h3></div>
        <div class="card-body">
            @foreach($complaint->comments as $comment)
                <div class="widget-item" style="padding-left:0;padding-right:0;">
                    <div class="widget-item-icon badge-primary" style="background:var(--color-primary-soft);color:var(--color-primary);"><i class="fa-solid fa-comment"></i></div>
                    <div class="widget-item-body">
                        <div class="widget-item-title">{{ $comment->user->name }}</div>
                        <div class="widget-item-desc">{{ $comment->comment }}</div>
                        <div class="text-xs text-muted mt-8">{{ $comment->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach

            <form action="{{ route('complaints.comment', $complaint) }}" method="POST" class="mt-16">
                @csrf
                <x-textarea label="Tambah Komentar" name="comment" required rows="3" />
                <x-button type="submit" variant="primary">Kirim Komentar</x-button>
            </form>
        </div>
    </div>
</x-layouts.app>
